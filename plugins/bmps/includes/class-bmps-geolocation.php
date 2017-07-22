<?php

/**
 * Geolocation class
 *
 * Handles geolocation and updating the geolocation database.
 *
 * This product includes GeoLite data created by MaxMind, available from http://www.maxmind.com.
 *
 * @author   Krutarth Patel
 * @category Admin
 * @package  BMPS/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class BMPS_Geolocation
 */
class BMPS_Geolocation
{
    /** URL to GEOLocations's database that is being used */
    const GEOLITE_DB      = 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz';
    const GEOLITE_IPV6_DB = 'http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz';

    /** @var array API endpoints to lookup for user IP address */

    private static $ip_lookup_apis = array(
        'icanhazip'         => 'http://icanhazip.com',
        'ipify'             => 'http://api.ipify.org/',
        'ipecho'            => 'http://ipecho.net/plain',
        'ident'             => 'http://ident.me',
        'whatismyipaddress' => 'http://bot.whatismyipaddress.com',
        'ip.appspot'        => 'http://ip.appspot.com'
    );

    /** @var array API endpoints for geolocating an IP address */
    private static $geoip_apis = array(
        'freegeoip'  => 'https://freegeoip.net/json/%s',
        'ipinfo.io'  => 'https://ipinfo.io/%s/json',
        'ip-api.com' => 'http://ip-api.com/json/%s',
    );

    /**
     * Hook in tabs.
     */
    public static function init() {
        // Only download the database from MaxMind if the geolocation function is enabled, or a plugin specifically requests it
        if ( 'geolocation' === get_option( 'bmps_default_customer_address' ) || apply_filters( 'bmps_geolocation_update_database_periodically', false ) ) {
            add_action( 'bmps_geoip_updater', array( __CLASS__, 'update_database' ) );
        }
        add_filter( 'pre_update_option_bmps_default_customer_address', array( __CLASS__, 'should_update_database' ), 10, 2 );
    }

    /**
     * Should trigger a DB update for the first time.
     * @param  string $new_value
     * @param  string $old_value
     * @return string
     */
    public static function should_update_database( $new_value, $old_value ) {
        if ( $new_value !== $old_value && 'geolocation' === $new_value ) {
            self::update_database();
        }
        return $new_value;
    }

    /**
     * Get current user IP Address.
     * @return string
     */
    public static function get_ip_address() {
        if ( isset( $_SERVER['X-Real-IP'] ) ) {
            return $_SERVER['X-Real-IP'];
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            // Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
            // Make sure we always only send through the first IP in the list which should always be the client IP.
            return trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
        } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }

    /**
     * Get user IP Address using an external service.
     * This is used mainly as a fallback for users on localhost where
     * get_ip_address() will be a local IP and non-geolocatable.
     * @return string
     */
    public static function get_external_ip_address() {
        $transient_name      = 'external_ip_address_' . self::get_ip_address();
        $external_ip_address = get_transient( $transient_name );

        if ( false === $external_ip_address ) {
            $external_ip_address     = '0.0.0.0';
            $ip_lookup_services      = apply_filters( 'bmps_geolocation_ip_lookup_apis', self::$ip_lookup_apis );
            $ip_lookup_services_keys = array_keys( $ip_lookup_services );
            shuffle( $ip_lookup_services_keys );

            foreach ( $ip_lookup_services_keys as $service_name ) {
                $service_endpoint = $ip_lookup_services[ $service_name ];
                $response         = wp_safe_remote_get( $service_endpoint, array( 'timeout' => 2 ) );

                if ( ! is_wp_error( $response ) && $response['body'] ) {
                    $external_ip_address = apply_filters( 'bmps_geolocation_ip_lookup_api_response', bmps_clean( $response['body'] ), $service_name );
                    break;
                }
            }

            set_transient( $transient_name, $external_ip_address, WEEK_IN_SECONDS );
        }

        return $external_ip_address;
    }

    /**
     * Geolocate an IP address.
     * @param  string $ip_address
     * @param  bool   $fallback If true, fallbacks to alternative IP detection (can be slower).
     * @param  bool   $api_fallback If true, uses geolocation APIs if the database file doesn't exist (can be slower).
     * @return array
     */
    public static function geolocate_ip( $ip_address = '', $fallback = true, $api_fallback = true ) {
        // Filter to allow custom geolocation of the IP address.
        $country_code = apply_filters( 'bmps_geolocate_ip', false, $ip_address, $fallback, $api_fallback );

        if ( false === $country_code ) {
            // If GEOIP is enabled in CloudFlare, we can use that (Settings -> CloudFlare Settings -> Settings Overview)
            if ( ! empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
                $country_code = sanitize_text_field( strtoupper( $_SERVER['HTTP_CF_IPCOUNTRY'] ) );
                // WP.com VIP has a variable available.
            } elseif ( ! empty( $_SERVER['GEOIP_COUNTRY_CODE'] ) ) {
                $country_code = sanitize_text_field( strtoupper( $_SERVER['GEOIP_COUNTRY_CODE'] ) );
                // VIP Go has a variable available also.
            } elseif ( ! empty( $_SERVER['HTTP_X_COUNTRY_CODE'] ) ) {
                $country_code = sanitize_text_field( strtoupper( $_SERVER['HTTP_X_COUNTRY_CODE'] ) );
            } else {
                $ip_address = $ip_address ? $ip_address : self::get_ip_address();

                if ( self::is_IPv6( $ip_address ) ) {
                    $database = self::get_local_database_path( 'v6' );
                } else {
                    $database = self::get_local_database_path();
                }

                if ( file_exists( $database ) ) {
                    $country_code = self::geolocate_via_db( $ip_address );
                } elseif ( $api_fallback ) {
                    $country_code = self::geolocate_via_api( $ip_address );
                } else {
                    $country_code = '';
                }

                if ( ! $country_code && $fallback ) {
                    // May be a local environment - find external IP
                    return self::geolocate_ip( self::get_external_ip_address(), false, $api_fallback );
                }
            }
        }

        return array(
            'country' => $country_code,
            'state'   => ''
        );
    }

    /**
     * Path to local db.
     * @param  string $version
     * @return string
     */
    public static function get_local_database_path( $version = 'v4' ) {
        $version    = ( 'v4' == $version ) ? '' : 'v6';
        $upload_dir = wp_upload_dir();

        return apply_filters( 'bmps_geolocation_local_database_path', $upload_dir['basedir'] . 'bmps/GeoIP' . $version . '.dat', $version );
    }

    /**
     * Update geoip database. Adapted from https://wordpress.org/plugins/geoip-detect/.
     */
    public static function update_database() {
        $logger = new BMPS_Logger();

        if ( ! is_callable( 'gzopen' ) ) {
            $logger->add( 'geolocation', 'Server does not support gzopen' );
            return;
        }

        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $tmp_databases = array(
            'v4' => download_url( self::GEOLITE_DB ),
            'v6' => download_url( self::GEOLITE_IPV6_DB )
        );

        foreach ( $tmp_databases as $tmp_database_version => $tmp_database_path ) {
            if ( ! is_wp_error( $tmp_database_path ) ) {
                $gzhandle = @gzopen( $tmp_database_path, 'r' );
                $handle   = @fopen( self::get_local_database_path( $tmp_database_version ), 'w' );

                if ( $gzhandle && $handle ) {
                    while ( $string = gzread( $gzhandle, 4096 ) ) {
                        fwrite( $handle, $string, strlen( $string ) );
                    }
                    gzclose( $gzhandle );
                    fclose( $handle );
                } else {
                    $logger->add( 'geolocation', 'Unable to open database file' );
                }
                @unlink( $tmp_database_path );
            } else {
                $logger->add( 'geolocation', 'Unable to download GeoIP Database: ' . $tmp_database_path->get_error_message() );
            }
        }
    }
}