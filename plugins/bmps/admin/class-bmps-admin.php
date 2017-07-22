<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.sourcefragment.com
 * @since      1.0.0
 *
 * @package    Bmps
 * @subpackage Bmps/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bmps
 * @subpackage Bmps/admin
 * @author     Krutarth Patel <krutarth@sourcefragment.com>
 */
class Bmps_Admin {

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
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bmps_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bmps_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/select2/select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_admin', plugin_dir_url(__FILE__) . 'css/bmps-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Bmps_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Bmps_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script('jquery-core');
        wp_enqueue_script($this->plugin_name . 'select2', plugin_dir_url(__FILE__) . 'js/select2/select2.min.js', array('jquery'), $this->version, false);
        wp_register_script($this->plugin_name . '-tool-tip', plugin_dir_url(__FILE__) . 'js/jquery-tiptip/jquery.tiptip.js', array('jquery'), $this->version, false);
        wp_register_script($this->plugin_name . '-parking-metaboxes', plugin_dir_url(__FILE__) . 'js/metaboxes/metaboxes.js', array('jquery', 'bmps-tool-tip'), $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/bmps-admin.js', array('jquery', 'bmps-tool-tip'), $this->version, false);
    }

}
