<?php

/**
 * Main function for returning products, uses the WC_Product_Factory class.
 *
 * @since 2.2.0
 *
 * @param mixed $the_product Post object or post ID of the product.
 * @param array $deprecated Previously used to pass arguments to the factory, e.g. to force a type.
 * @return WC_Product|null
 */
function bmps_get_parking($the_product = false, $deprecated = array()) {
    if (!did_action('bmps_init')) {
       // wc_doing_it_wrong(__FUNCTION__, __('wc_get_product should not be called before the woocommerce_init action.', 'woocommerce'), '2.5');
        return false;
    }
    if (!empty($deprecated)) {
       // wc_deprecated_argument('args', '3.0', 'Passing args to wc_get_product is deprecated. If you need to force a type, construct the product class directly.');
    }
    //return WC()->product_factory->get_product($the_product, $deprecated);
}

/**
 * Queue a parking for syncing at the end of the request.
 *
 * @param  int $parking_id
 */
function bmps_deferred_parking_sync( $parking_id) {
	global $bmps_deferred_parking_sync;
	
	if ( empty( $bmps_deferred_parking_sync) ) {
		$bmps_deferred_parking_sync= array();
	}
	
	$bmps_deferred_parking_sync[] = $parking_id;
}