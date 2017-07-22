<?php

/**
 * WooCommerce Settings Page/Tab
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin
 * @version     2.1.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Plugin Settings Main Class
 */
abstract class BMPS_Settings_Main {

    /**
     * Setting page id.
     *
     * @var string
     */
    protected $id = '';

    /**
     * Setting page label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter('bmps_settings_tabs_array', array($this, 'add_settings_page'), 20);
        add_action('bmps_sections_' . $this->id, array($this, 'render_sections'));
        add_action('bmps_settings_' . $this->id, array($this, 'render'));
        add_action('bmps_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * Get settings page ID.
     * @since 3.0.0
     * @return string
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get settings page label.
     * @since 3.0.0
     * @return string
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Add this page to settings.
     */
    public function add_settings_page($pages) {
        $pages[$this->id] = $this->label;
        return $pages;
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings() {
        return apply_filters('bmps_get_settings_' . $this->id, array());
    }

    /**
     * Get sections.
     *
     * @return array
     */
    public function get_sections() {
        return apply_filters('bmps_get_sections_' . $this->id, array());
    }

    /**
     * Render sections.
     */
    public function render_sections() {
        global $current_section;

        $sections = $this->get_sections();

        if (empty($sections) || 1 === sizeof($sections)) {
            return;
        }

        echo '<ul class="subsubsub">';

        $array_keys = array_keys($sections);

        foreach ($sections as $id => $label) {
            echo '<li><a href="' . admin_url('admin.php?page=bmps-settings&tab=' . $this->id . '&section=' . sanitize_title($id)) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end($array_keys) == $id ? '' : '|' ) . ' </li>';
        }

        echo '</ul><br class="clear" />';
    }

    /**
     * Output the settings.
     */
    public function render() {
        $settings = $this->get_settings();

        BMPS_Admin_Settings::render_fields($settings);
    }

    /**
     * Save settings.
     */
    public function save() {
        global $current_section;

        $settings = $this->get_settings();
        WC_Admin_Settings::save_fields($settings);

        if ($current_section) {
            do_action('woocommerce_update_options_' . $this->id . '_' . $current_section);
        }
    }

}
