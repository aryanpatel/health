<?php

if (!defined('ABSPATH')) {
    exit;
}

class BMPS_Meta_Box_Parking_Data {

    /**
     * Render the metabox.
     *
     * @param WP_Post $post
     */
    public static function render($post) {
        global $thepostid, $parking_object;

        $thepostid = $post->ID;
        $parking_object = $thepostid ? bmps_get_parking($thepostid) : new WC_Product;

        include( 'templates/htm-parking-data-section.php' );
    }

    /**
     * Show tab content/settings.
     */
    private static function output_tabs() {
        global $post, $thepostid, $parking_object;

        include( 'templates/html-parking-pricing-data.php' );
        include( 'templates/html-parking-location-data.php' );
        include( 'templates/html-parking-feature-data.php' );
        include( 'templates/html-parking-suitable-data.php' );
        include( 'templates/html-parking-access-hours-data.php' );
//        include( 'templates/html-product-data-advanced.php' );
        wp_enqueue_script('bmps-tool-tip');
        wp_enqueue_script('bmps-parking-metaboxes');
    }

    /**
     * Return array of tabs to show.
     * @return array
     */
    private static function get_parking_data_tabs() {
        return apply_filters('bmps_parking_data_tabs', array(
            'pricing' => array(
                'label' => __('Pricing', BMPS_PLUGIN_TEXTDOMAIN),
                'target' => 'parking_pricing_option',
                'class' => array(),
            ),
            'location' => array(
                'label' => __('Location', BMPS_PLUGIN_TEXTDOMAIN),
                'target' => 'parking_location_option',
                'class' => array(),
            ),
            'features' => array(
                'label' => __('Features', BMPS_PLUGIN_TEXTDOMAIN),
                'target' => 'parking_features_options',
                'class' => array(),
            ),
            'suitable_for' => array(
                'label' => __('Suitable For', BMPS_PLUGIN_TEXTDOMAIN),
                'target' => 'parking_suitable_for',
                'class' => array(),
            ),
            'access_hours' => array(
                'label' => __('Aaccess Hours', BMPS_PLUGIN_TEXTDOMAIN),
                'target' => 'parking_access_hours',
                'class' => array(),
            )
        ));
    }

    /**
     * Save meta box data.
     */
    public static function save($post_id, $post) {
        $parking = new BMPS_Parking($post_id);
        $errors = $parking->set_porps(array(
            'rental_type_daily' 	=> !empty( $_POST['_rental_type_daily'] ),
        	'hourly_price' 			=> bmps_clean( $_POST['_hourly_price'] ),
        	'daily_price' 			=> bmps_clean( $_POST['_daily_price'] ),
        	'rental_type_monthly' 	=> !empty( $_POST['_rental_type_monthly'] ),
        	'monthly_price' 		=> bmps_clean( $_POST['_monthly_price'] ),
            'parking_slots' 		=> !empty( $_POST['_parking_slots'] ),
        	'parking_location'		=> isset( $_POST['_parking_location'] ) ? bmps_clean( $_POST['_parking_location'] ) : '',
        	'google_map_link'		=> isset( $_POST['_google_map_link']) ? bmps_clean( $_POST['_google_map_link']) : '',
        	'parking_features'		=> !empty( $_POST['_parking_features'] ) ? implode(',', $_POST['_parking_features']) : '',
        	'parking_suitable_for'	=> !empty( $_POST['_parking_suitable_for'] ) ? implode(',', $_POST['_parking_suitable_for']) : ''
        ));
        if (is_wp_error($errors)) {
            BMPS_Admin_Meta_Boxes::add_error($errors->get_error_message());
        }

        /**
         * set properties before save
         */
        do_action('bmps_admin_process_product_object', $parking);
        $parking->save();
exit;
        if ($product->is_type('variable')) {
            $product->get_data_store()->sync_variation_names($product, wc_clean($_POST['original_post_title']), wc_clean($_POST['post_title']));
        }

        do_action('bmps_process_parking_meta_' . $product_type, $post_id);
    }

}
