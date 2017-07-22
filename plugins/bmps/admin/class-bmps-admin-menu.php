<?php

class BMPS_Admin_Menus {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Add menu items.
     */
    public function admin_menu() {
        global $menu;

        if (current_user_can('manage_parkings')) {
            $menu[] = array('', 'read', 'separator-parking', '', 'wp-menu-separator bmps');
        }

        add_menu_page(__('Bookings', BMPS_PLUGIN_TEXTDOMAIN), __('Bookings', BMPS_PLUGIN_TEXTDOMAIN), 'manage_parkings', BMPS_PLUGIN_TEXTDOMAIN, null, null, '55.5');
    }

    /**
     * Add menu item.
     */
//    public function reports_menu() {
//        if (current_user_can('manage_woocommerce')) {
//            add_submenu_page('woocommerce', __('Reports', 'woocommerce'), __('Reports', 'woocommerce'), 'view_woocommerce_reports', 'wc-reports', array($this, 'reports_page'));
//        } else {
//            add_menu_page(__('Sales reports', 'woocommerce'), __('Sales reports', 'woocommerce'), 'view_woocommerce_reports', 'wc-reports', array($this, 'reports_page'), null, '55.6');
//        }
//    }

    /**
     * Add menu item.
     */
    public function settings_menu() {
        $settings_page = add_submenu_page(BMPS_PLUGIN_TEXTDOMAIN, __('Bookings settings', BMPS_PLUGIN_TEXTDOMAIN), __('Settings', BMPS_PLUGIN_TEXTDOMAIN), 'manage_parkings', 'bmps-settings', array($this, 'settings_page'));

        add_action('load-' . $settings_page, array($this, 'settings_page_init'));
    }

    /**
     * Loads gateways and shipping methods into memory for use within settings.
     */
    public function settings_page_init() {
        //WC()->payment_gateways();
        //WC()->shipping();
    }

    /**
     * Init the settings page.
     */
    public function settings_page() {
        BMPS_Admin_Settings::render();
    }

}
