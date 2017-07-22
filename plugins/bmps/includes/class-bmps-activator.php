<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.sourcefragment.com
 * @since      1.0.0
 *
 * @package    Bmps
 * @subpackage Bmps/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bmps
 * @subpackage Bmps/includes
 * @author     Krutarth Patel <krutarth@sourcefragment.com>
 */
class Bmps_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        self::install();
    }

    /**
     * Install BMPS
     */
    private static function install() {
        global $wpdb;

        if (!defined('BMPS_INSTALLING')) {
            define('BMPS_INSTALLING', true);
        }
        add_filter('plugin_action_links_' . BMPS_PLUGIN_BASENAME, array(__CLASS__, 'add_action_links'));
        self::create_options();
        self::create_roles();
    }

    /**
     * Create roles and capabilities.
     */
    public static function create_roles() {
        global $wp_roles;

        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Customer role
        add_role('customer', __('Customer', BMPS_PLUGIN_TEXTDOMAIN), array(
            'read' => true,
        ));

        // Shop manager role
        add_role('shop_manager', __('Shop manager', BMPS_PLUGIN_TEXTDOMAIN), array(
            'level_9' => true,
            'level_8' => true,
            'level_7' => true,
            'level_6' => true,
            'level_5' => true,
            'level_4' => true,
            'level_3' => true,
            'level_2' => true,
            'level_1' => true,
            'level_0' => true,
            'read' => true,
            'read_private_pages' => true,
            'read_private_posts' => true,
            'edit_users' => true,
            'edit_posts' => true,
            'edit_pages' => true,
            'edit_published_posts' => true,
            'edit_published_pages' => true,
            'edit_private_pages' => true,
            'edit_private_posts' => true,
            'edit_others_posts' => true,
            'edit_others_pages' => true,
            'publish_posts' => true,
            'publish_pages' => true,
            'delete_posts' => true,
            'delete_pages' => true,
            'delete_private_pages' => true,
            'delete_private_posts' => true,
            'delete_published_pages' => true,
            'delete_published_posts' => true,
            'delete_others_posts' => true,
            'delete_others_pages' => true,
            'manage_categories' => true,
            'manage_links' => true,
            'moderate_comments' => true,
            'unfiltered_html' => true,
            'upload_files' => true,
            'export' => true,
            'import' => true,
            'list_users' => true,
        ));

        $capabilities = self::get_core_capabilities();

        foreach ($capabilities as $cap_group) {
            foreach ($cap_group as $cap) {
                $wp_roles->add_cap('shop_manager', $cap);
                $wp_roles->add_cap('administrator', $cap);
            }
        }
    }

    /**
     * Get capabilities for WooCommerce - these are assigned to admin/shop manager during installation or reset.
     *
     * @return array
     */
    private static function get_core_capabilities() {
        $capabilities = array();

        $capabilities['core'] = array(
            'manage_parkings',
            'view_parking_reports',
        );

        $capability_types = array('parking', 'bookings', 'booking_cupons', 'booking_webhook');

        foreach ($capability_types as $capability_type) {

            $capabilities[$capability_type] = array(
                // Post type
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "read_private_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_private_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_private_{$capability_type}s",
                "edit_published_{$capability_type}s",
                // Terms
                "manage_{$capability_type}_terms",
                "edit_{$capability_type}_terms",
                "delete_{$capability_type}_terms",
                "assign_{$capability_type}_terms",
            );
        }

        return $capabilities;
    }

    /**
     * Default options.
     *
     * Sets up the default options used on the settings page.
     */
    private static function create_options() {
        add_option('bmps_allowed_countries', 'all');
        add_option('bmps_default_currency', 'USD');
        include_once BMPS_PLUGIN_DIR . 'admin/class-bmps-admin-settings.php';
    }

    public static function add_action_links() {
        $action_links = array(
            'settings' => '<a href="' . admin_url('admin.php?page=bmps-settings') . '" aria-label="' . esc_attr__('View Parking settings', BMPS_PLUGIN_TEXTDOMAIN) . '">' . esc_html__('Settings', BMPS_PLUGIN_TEXTDOMAIN) . '</a>',
        );

        return array_merge($action_links, $links);
    }

}
