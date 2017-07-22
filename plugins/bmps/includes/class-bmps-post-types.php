<?php

class BMPS_Post_Type {

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

    public function register_post_types() {

        if (!is_blog_installed() || post_type_exists('parking')) {
            return;
        }

        do_action('bmps_register_post_type');

        //$permalinks = wc_get_permalink_structure();

        register_post_type('parking', apply_filters('bmps_register_post_type_parking', array(
            'labels' => array(
                'name' => __('Parkings', BMPS_PLUGIN_TEXTDOMAIN),
                'singular_name' => __('Parking', BMPS_PLUGIN_TEXTDOMAIN),
                'menu_name' => _x('Parkings', 'Admin menu name', BMPS_PLUGIN_TEXTDOMAIN),
                'add_new' => __('Add parking', BMPS_PLUGIN_TEXTDOMAIN),
                'add_new_item' => __('Add new parking', BMPS_PLUGIN_TEXTDOMAIN),
                'edit' => __('Edit', BMPS_PLUGIN_TEXTDOMAIN),
                'edit_item' => __('Edit parking', BMPS_PLUGIN_TEXTDOMAIN),
                'new_item' => __('New parking', BMPS_PLUGIN_TEXTDOMAIN),
                'view' => __('View parking', BMPS_PLUGIN_TEXTDOMAIN),
                'view_item' => __('View parking', BMPS_PLUGIN_TEXTDOMAIN),
                'search_items' => __('Search parking', BMPS_PLUGIN_TEXTDOMAIN),
                'not_found' => __('No Parkings found', BMPS_PLUGIN_TEXTDOMAIN),
                'not_found_in_trash' => __('No Parkings found in trash', BMPS_PLUGIN_TEXTDOMAIN),
                'featured_image' => __('Parkings image', BMPS_PLUGIN_TEXTDOMAIN),
                'set_featured_image' => __('Set parking image', BMPS_PLUGIN_TEXTDOMAIN),
                'remove_featured_image' => __('Remove parking image', BMPS_PLUGIN_TEXTDOMAIN),
                'use_featured_image' => __('Use as parking image', BMPS_PLUGIN_TEXTDOMAIN),
                'insert_into_item' => __('Insert into parking', BMPS_PLUGIN_TEXTDOMAIN),
                'uploaded_to_this_item' => __('Uploaded to this parking', BMPS_PLUGIN_TEXTDOMAIN),
                'filter_parkings_list' => __('Filter parkings', BMPS_PLUGIN_TEXTDOMAIN),
                'parkings_list_navigation' => __('Parkings navigation', BMPS_PLUGIN_TEXTDOMAIN),
                'parkings_list' => __('Products list', BMPS_PLUGIN_TEXTDOMAIN),
            ),
            'description' => __('This is where you can add new Parking.', BMPS_PLUGIN_TEXTDOMAIN),
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'parking',
            'map_meta_cap' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
            'rewrite' => true,
            'query_var' => true,
            'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'publicize', 'wpcom-markdown'),
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
                        )
                )
        );

        bmps_register_booking_type(
                'booking', apply_filters('bmps_register_post_type_bookings', array(
            'labels' => array(
                'name' => __('Bookings', BMPS_PLUGIN_TEXTDOMAIN),
                'singular_name' => _x('Booking', 'booking post type singular name', BMPS_PLUGIN_TEXTDOMAIN),
                'add_new' => __('Add booking', BMPS_PLUGIN_TEXTDOMAIN),
                'add_new_item' => __('Add new booking', BMPS_PLUGIN_TEXTDOMAIN),
                'edit' => __('Edit', BMPS_PLUGIN_TEXTDOMAIN),
                'edit_item' => __('Edit order', BMPS_PLUGIN_TEXTDOMAIN),
                'new_item' => __('New booking', BMPS_PLUGIN_TEXTDOMAIN),
                'view' => __('View booking', BMPS_PLUGIN_TEXTDOMAIN),
                'view_item' => __('View booking', BMPS_PLUGIN_TEXTDOMAIN),
                'search_items' => __('Search booking', BMPS_PLUGIN_TEXTDOMAIN),
                'not_found' => __('No bookings found', BMPS_PLUGIN_TEXTDOMAIN),
                'not_found_in_trash' => __('No bookings found in trash', BMPS_PLUGIN_TEXTDOMAIN),
                'parent' => __('Parent bookings', BMPS_PLUGIN_TEXTDOMAIN),
                'menu_name' => _x('Bookings', 'Admin menu name', BMPS_PLUGIN_TEXTDOMAIN),
                'filter_items_list' => __('Filter booking', BMPS_PLUGIN_TEXTDOMAIN),
                'items_list_navigation' => __('Bookings navigation', BMPS_PLUGIN_TEXTDOMAIN),
                'items_list' => __('Bookings list', BMPS_PLUGIN_TEXTDOMAIN),
            ),
            'description' => __('This is where bookings are stored.', BMPS_PLUGIN_TEXTDOMAIN),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'bookings',
            'map_meta_cap' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_in_menu' => current_user_can('manage_parkings') ? 'bmps' : true,
            'hierarchical' => false,
            'show_in_nav_menus' => false,
            'rewrite' => false,
            'query_var' => false,
            'supports' => array('title', 'comments', 'custom-fields'),
            'has_archive' => false,
                        )
                )
        );
    }

}
