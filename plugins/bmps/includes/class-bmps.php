<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.sourcefragment.com
 * @since      1.0.0
 *
 * @package    Bmps
 * @subpackage Bmps/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bmps
 * @subpackage Bmps/includes
 * @author     Krutarth Patel <krutarth@sourcefragment.com>
 */
class Bmps {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Bmps_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'bmps';
        $this->version = '1.0.0';
        $this->__define_constants();
        $this->load_dependencies();
        $this->__define_properties();
        $this->global_hooks();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function __define_properties() {
        $this->register_new_post_types = new BMPS_Post_Type($this->plugin_name, $this->version);
        $this->admin_menu = new BMPS_Admin_Menus($this->plugin_name, $this->version);

        $this->bmps_admin_meta_boxes = new BMPS_Admin_Meta_Boxes($this->plugin_name, $this->version);
        $this->parking_post_type_display = new BMPS_Admin_Parking_Display($this->plugin_name, $this->version);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Bmps_Loader. Orchestrates the hooks of the plugin.
     * - Bmps_i18n. Defines internationalization functionality.
     * - Bmps_Admin. Defines all hooks for the admin area.
     * - Bmps_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bmps-loader.php';
	
        /**
         * Interfaces
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/interfaces/class-bmps-object-data-storage-interface.php';
        
        /**
         * Data formatting functions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/functions/bmps-data-formatting.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/functions/bmps-booking-functions.php'; // Bookings functions

        /**
         * Core functions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/functions/bmps-core-functions.php';

        /**
         * function that is responsible for conditional data
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/functions/bmps-condition-functions.php';

        /**
         * The class responsible for registering custom post types
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bmps-post-types.php'; // Registers post types
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bmps-i18n.php';
		
        /**
         * The core class for parking
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/abstract/abstract-bmps-data.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/abstract/abstract-bmps-parking-base.php';
        
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-bmps-admin.php';
        
        /**
         * The class responsible for saving parking post types
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bmps-parking.php';

        /**
         * Functions for parkings
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/functions/bmps-parking-functions.php';

        /**
         * Functions for meta boxes
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/functions/meta-boxes-functions.php';

        /**
         * The class responsible for adding meta boxes to custom post types
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-bmps-admin-meta-boxes.php';

        /**
         * The class responsible for modifying view of custom post type "Parkings"
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-bmps-admin-parkings-display.php';

        /**
         * The class required for plugin settings
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-bmps-admin-settings.php';

        /**
         * The class responsible for Admin menu
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-bmps-admin-menu.php';
	
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-bmps-public.php';
		
        /**
         * Data Factory
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bmps-data-storage.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/data-storage/class-bmps-data-storage-wp.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/data-storage/class-bmps-parking-data-storage-cpt.php';
        
        
        /**
         * The class responsible for adding extra information to error exceptions
         */
        
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bmps-data-exception.php' ;
        
        /**
         * Functions
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/bmps-formatting-functions.php';
        
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bmps-countries.php';
        $this->loader = new Bmps_Loader();
    }

    /**
     * Global Hooks
     * 
     * Some hooks needs to run both admin and publc sides
     * 
     * @since 1.0.0
     * @access private
     */
    private function global_hooks() {
        $this->countries = new BMPS_Countries();
        $this->loader->add_action('init', $this->register_new_post_types, 'register_post_types');
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Bmps_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Bmps_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_basename = plugin_basename(plugin_dir_path(realpath(dirname(__FILE__))) . $this->plugin_name . '.php');
        $plugin_admin = new Bmps_Admin($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $this->admin_menu, 'admin_menu', 9);
        $this->loader->add_action('admin_menu', $this->admin_menu, 'settings_menu', 50);

        /**
         * Hooks for meta boxes
         */
        $this->loader->add_action('add_meta_boxes', $this->bmps_admin_meta_boxes, 'remove_meta_boxes', 10);
        $this->loader->add_action('add_meta_boxes', $this->bmps_admin_meta_boxes, 'add_meta_boxes', 30);
        $this->loader->add_action('save_post',$this->bmps_admin_meta_boxes, 'save_meta_boxes',1,2);
        //To show error related to parking meta boxes on save post
        $this->loader->add_action('admin_notices', $this->bmps_admin_meta_boxes, 'output_errors');
        $this->loader->add_action('shutdown', $this->bmps_admin_meta_boxes, 'save_errors');
        /**
         * Hooks for class-bmps-admin-parking-display
         */
        $this->loader->add_action('manage_parking_posts_columns', $this->parking_post_type_display, 'parking_columns');
        $this->loader->add_action('manage_edit-parking_sortable_columns', $this->parking_post_type_display, 'parking_sortable_columns');
        //$this->loader->add_action('manage_product_posts_custom_column', $this->parking_post_type_display, 'render_parking_columns');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Bmps_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Bmps_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Define BMPS constants
     */
    private function __define_constants() {
        $upload_dir = wp_upload_dir();
        $this->__define('BMPS_PLUGIN_DIR', plugin_dir_path(__DIR__));
        $this->__define('BMPS_PLUGIN_URL', plugin_dir_url(__DIR__));
        $this->__define('BMPS_PLUGIN_BASENAME', plugin_basename(__FILE__));
        $this->__define('BMPS_VERSION', $this->version);
        $this->__define('BMPS_PLUGIN_TEXTDOMAIN', 'bmps');
        $this->__define('BMPS_ROUNDING_PRECISION', 4);
        $this->__define('BMPS_DISCOUNT_ROUNDING_MODE', 2);
        $this->__define('BMPS_TAX_ROUNDING_MODE', 'yes' === get_option('parkins_space_prices_include_tax', 'no') ? 2 : 1 );
        $this->__define('BMPS_DELIMITER', '|');
        $this->__define('BMPS_SESSION_CACHE_GROUP', 'bmps_session_id');
    }

    /**
     * Define constant if not already set.
     *
     * @param  string $name
     * @param  string|bool $value
     */
    private function __define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    public function init() {
        $this->countries = new BMPS_Countries();
    }

}
