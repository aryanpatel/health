<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Book My Parking space countries
 *
 * The Book My Parking space countries class. It stores country/state data.
 *
 * @class       BMPS_Countries
 * @version     1.0.0
 * @package     BMPS/Classes
 * @category    Class
 * @author      Krutarth
 */
class BMPS_Countries {

    /** @var array Array of locales */
    public $locale;

    /** @var array Array of address formats for locales */
    public $address_formats;

    /**
     * Auto-load in-accessible properties on demand.
     * @param  mixed $key
     * @return mixed
     */
    public function __get($key) {
        if ('countries' == $key) {
            return $this->get_countries();
        } elseif ('states' == $key) {
            return $this->get_states();
        }
    }

    /**
     * Get List of all countries
     * @return array
     */
    public function get_countries() {
        if (empty($this->countries)) {
            $this->countries = apply_filters('bmps_get_countries_list', include( BMPS_PLUGIN_DIR . '/i18n/countries.php' ));
            if (apply_filters('bmps_sort_countries_list', true)) {
                asort($this->countries);
            }
        }
        return $this->countries;
    }

    /**
     * Get List of all Continants
     * @return array
     */
    public function get_continants() {
        if (empty($this->continents)) {
            $this->continents = apply_filters('bmps_continents_list', include( BMPS_PLUGIN_DIR . '/i18n/continents.php' ));
        }
        return $this->continents;
    }

    /**
     * Get continent code for a country code.
     * @since 1.0.0
     * @param string $continantCode string
     * @return string
     */
    public function get_continent_code_for_country($continantCode) {
        $continantCode = trim(strtoupper($continantCode));
        $continents = $this->get_continents();
        $continents_and_continantcodes = wp_list_pluck($continents, 'countries');
        foreach ($continents_and_continantcodes as $continent_code => $countries) {
            if (false !== array_search($continantCode, $countries)) {
                return $continent_code;
            }
        }
        return '';
    }

    /**
     * Load the states.
     */
    public function load_country_states() {
        global $states;

        // States set to array() are blank i.e. the country has no use for the state field.
        $states = array(
            'AF' => array(),
            'AT' => array(),
            'AX' => array(),
            'BE' => array(),
            'BI' => array(),
            'CZ' => array(),
            'DE' => array(),
            'DK' => array(),
            'EE' => array(),
            'FI' => array(),
            'FR' => array(),
            'GP' => array(),
            'GF' => array(),
            'IS' => array(),
            'IL' => array(),
            'KR' => array(),
            'KW' => array(),
            'LB' => array(),
            'MQ' => array(),
            'NL' => array(),
            'NO' => array(),
            'PL' => array(),
            'PT' => array(),
            'RE' => array(),
            'SG' => array(),
            'SK' => array(),
            'SI' => array(),
            'LK' => array(),
            'SE' => array(),
            'VN' => array(),
            'YT' => array(),
        );

        // Load only the state files the shop owner wants/needs.
        $allowed = array_merge($this->get_allowed_countries());
        
        if (!empty($allowed)) {
            foreach ($allowed as $code => $country) {
                if (!isset($states[$code]) && file_exists(BMPS_PLUGIN_DIR . '/i18n/states/' . $code . '.php')) {
                    include( BMPS_PLUGIN_DIR . '/i18n/states/' . $code . '.php' );
                }
            }
        }
        $this->states = apply_filters('bmps_states', $states);
    }

    /**
     * Get the allowed countries for the store.
     * @return array
     */
    public function get_allowed_countries() {
        if ('all' === get_option('bmps_allowed_countries')) {
            return $this->countries;
        }

        if ('all_except' === get_option('bmps_allowed_countries')) {
            $except_countries = get_option('bmps_all_except_countries', array());

            if (!$except_countries) {
                return $this->countries;
            } else {
                $all_except_countries = $this->countries;
                foreach ($except_countries as $country) {
                    unset($all_except_countries[$country]);
                }
                return apply_filters('bmps_countries_allowed_countries', $all_except_countries);
            }
        }

        $countries = array();

        $raw_countries = get_option('bmps_specific_allowed_countries', array());

        if ($raw_countries) {
            foreach ($raw_countries as $country) {
                $countries[$country] = $this->countries[$country];
            }
        }
        return apply_filters('bmps_countries_allowed_countries', $countries);
    }

    /**
     * Outputs the list of countries and states for use in dropdown boxes.
     * @param string $selected_country (default: '')
     * @param string $selected_state (default: '')
     * @param bool $escape (default: false)
     * @param bool   $escape (default: false)
     */
    public function country_dropdown_options($selected_country = '', $selected_state = '', $escape = false) {
        if ($this->countries)
            foreach ($this->countries as $key => $value) :
                if ($states = $this->get_states($key)) :
                    echo '<optgroup label="' . esc_attr($value) . '">';
                    foreach ($states as $state_key => $state_value) :
                        echo '<option value="' . esc_attr($key) . ':' . $state_key . '"';

                        if ($selected_country == $key && $selected_state == $state_key) {
                            echo ' selected="selected"';
                        }

                        echo '>' . $value . ' &mdash; ' . ( $escape ? esc_js($state_value) : $state_value ) . '</option>';
                    endforeach;
                    echo '</optgroup>';
                else :
                    echo '<option';
                    if ($selected_country == $key && '*' == $selected_state) {
                        echo ' selected="selected"';
                    }
                    echo ' value="' . esc_attr($key) . '">' . ( $escape ? esc_js($value) : $value ) . '</option>';
                endif;
            endforeach;
    }

    /**
     * Get the states for a country.
     * @param  string $cc country code
     * @return array of states
     */
    public function get_states($cc = null) {
        if (empty($this->states)) {
            $this->load_country_states();
        }

        if (!is_null($cc)) {
            return isset($this->states[$cc]) ? $this->states[$cc] : false;
        } else {
            return $this->states;
        }
    }

}
