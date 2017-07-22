<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BMPS_Admin_Meta_Boxes {

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

    /**
     * Is meta boxes saved once?
     *
     * @var boolean
     */
    private static $saved_meta_boxes = false;

    /**
     * Meta box error messages.
     *
     * @var array
     */
    public static $meta_box_errors = array();

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/metaboxes/class-bmps-meta-box-short-description.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/metaboxes/class-bmps-meta-box-parking-data.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/metaboxes/class-bmps-meta-box-parking-images.php';

        add_action('bmps_process_parking_meta', 'BMPS_Meta_Box_Parking_Data::save', 10, 2);
        add_action('bmps_process_parking_meta', 'BMPS_Meta_Box_Parking_Images::save', 20, 2);
    }

    public function add_meta_boxes() {
        $screen = get_current_screen();
        $screen_id = $screen ? $screen->id : '';
        /**
         * Parkings
         */
        add_meta_box('postexcerpt', __('Short description', BMPS_PLUGIN_TEXTDOMAIN), 'BMPS_Meta_Box_Short_Description::render', 'parking', 'normal');
        add_meta_box('bmps-parking-data', __('Parking data', BMPS_PLUGIN_TEXTDOMAIN), 'BMPS_Meta_Box_Parking_Data::render', 'parking', 'normal', 'high');
        add_meta_box('bmps-parking-images', __('Parking gallery', BMPS_PLUGIN_TEXTDOMAIN), 'BMPS_Meta_Box_Parking_Images::render', 'parking', 'side', 'low');
    }

    /**
     * Remove bloat.
     */
    public function remove_meta_boxes() {
        remove_meta_box('postexcerpt', 'parking', 'normal');
    }

    /**
     * Add an error message.
     * @param string $text
     */
    public static function add_error($text) {
        self::$meta_box_errors[] = $text;
    }

    /**
     * Save errors to an option.
     */
    public function save_errors() {
        update_option('woocommerce_meta_box_errors', self::$meta_box_errors);
    }

    /**
     * Show any stored error messages.
     */
    public function output_errors() {
        $errors = array_filter((array) get_option('bmps_meta_box_errors'));

        if (!empty($errors)) {

            echo '<div id="bmps_errors" class="error notice is-dismissible">';

            foreach ($errors as $error) {
                echo '<p>' . wp_kses_post($error) . '</p>';
            }

            echo '</div>';

            // Clear
            delete_option('bmps_meta_box_errors');
        }
    }

    /**
     * Check if we're saving, the trigger an action based on the post type.
     *
     * @param  int $post_id
     * @param  object $post
     */
    public function save_meta_boxes($post_id, $post) {
        // $post_id and $post are required
        if (empty($post_id) || empty($post) || self::$saved_meta_boxes) {
            return;
        }

        // Dont' save meta boxes for revisions or autosaves
        if (defined('DOING_AUTOSAVE') || is_int(wp_is_post_revision($post)) || is_int(wp_is_post_autosave($post))) {
            return;
        }

        // Check the nonce
        if (empty($_POST['bmps_meta_nonce']) || !wp_verify_nonce($_POST['bmps_meta_nonce'], 'bmps_save_data')) {
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events
        if (empty($_POST['post_ID']) || $_POST['post_ID'] != $post_id) {
            return;
        }

        // Check user has permission to edit
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // We need this save event to run once to avoid potential endless loops. This would have been perfect:
        // remove_action( current_filter(), __METHOD__ );
        // But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
        // When that is patched in core we can use the above. For now:
        self::$saved_meta_boxes = true;

        // Check the post type
        if (in_array($post->post_type, bmps_get_booking_types('booking-meta-boxes'))) {
            //do_action('woocommerce_process_shop_order_meta', $post_id, $post);
            do_action('bmps_process_parking_booking_meta', $post_id, $post);
        } elseif (in_array($post->post_type, array('parking', 'shop_coupon'))) {
            //do_action('woocommerce_process_' . $post->post_type . '_meta', $post_id, $post);
            do_action('bmps_process_' . $post->post_type . '_meta', $post_id, $post);
        }
    }

}
