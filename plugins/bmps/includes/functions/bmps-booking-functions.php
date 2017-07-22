<?php
/**
 * WooCommerce Order Functions
 *
 * Functions for order specific things.
 *
 * @author      WooThemes
 * @category    Core
 * @package     WooCommerce/Functions
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper for get_posts specific to bookings.
 *
 * This function should be used for order retrieval so that when we move to
 * custom tables, functions still work.
 *
 * Args:
 *      status array|string List of order statuses to find
 *      type array|string Order type, e.g. shop_order or shop_order_refund
 *      parent int post/order parent
 *      customer int|string|array User ID or billing email to limit bookings to a
 *          particular user. Accepts array of values. Array of values is OR'ed. If array of array is passed, each array will be AND'ed.
 *          e.g. test@test.com, 1, array( 1, 2, 3 ), array( array( 1, 'test@test.com' ), 2, 3 )
 *      limit int Maximum of bookings to retrieve.
 *      offset int Offset of bookings to retrieve.
 *      page int Page of bookings to retrieve. Ignored when using the 'offset' arg.
 *      date_before string Get bookings before a certain date ( strtotime() compatibile string )
 *      date_after string Get bookings after a certain date ( strtotime() compatibile string )
 *      exclude array Order IDs to exclude from the query.
 *      orderby string Order by date, title, id, modified, rand etc
 *      order string ASC or DESC
 *      return string Type of data to return. Allowed values:
 *          ids array of order ids
 *          objects array of order objects (default)
 *      paginate bool If true, the return value will be an array with values:
 *          'bookings'        => array of data (return value above),
 *          'total'         => total number of bookings matching the query
 *          'max_num_pages' => max number of pages found
 *
 * @since  2.6.0
 * @param  array $args Array of args (above)
 * @return array|stdClass Number of pages and an array of order objects if
 *                             paginate is true, or just an array of values.
 */
function bmps_get_bookings( $args ) {
	$args = wp_parse_args( $args, array(
		'status'      => array_keys( bmps_get_booking_statuses() ),
		'type'        => bmps_get_booking_types( 'view-bookings' ),
		'parent'      => null,
		'customer'    => null,
		'email'       => '',
		'limit'       => get_option( 'posts_per_page' ),
		'offset'      => null,
		'page'        => 1,
		'exclude'     => array(),
		'orderby'     => 'date',
		'order'       => 'DESC',
		'return'      => 'objects',
		'paginate'    => false,
		'date_before' => '',
		'date_after'  => '',
	) );

	// Handle some BW compatibility arg names where wp_query args differ in naming.
	$map_legacy = array(
		'numberposts'    => 'limit',
		'post_type'      => 'type',
		'post_status'    => 'status',
		'post_parent'    => 'parent',
		'author'         => 'customer',
		'posts_per_page' => 'limit',
		'paged'          => 'page',
	);

	foreach ( $map_legacy as $from => $to ) {
		if ( isset( $args[ $from ] ) ) {
			$args[ $to ] = $args[ $from ];
		}
	}

	return WC_Data_Store::load( 'order' )->get_bookings( $args );
}

/**
 * Main function for returning bookings, uses the WC_Order_Factory class.
 *
 * @since  2.2
 * @param  mixed $the_booking Post object or post ID of the order.
 * @return WC_Order|WC_Refund
 */
function bmps_get_booking( $the_booking = false ) {
	if ( ! did_action( 'bmps_after_register_post_type' ) ) {
		//bmps_doing_it_wrong( __FUNCTION__, __( 'bmps_get_booking should not be called before post types are registered (bmps_after_register_post_type action).', BMPS_PLUGIN_TEXTDOMAIN ), '2.5' );
		return false;
	}
	return WC()->order_factory->get_booking( $the_booking );
}

/**
 * Get all order statuses.
 *
 * @since 2.2
 * @used-by WC_Order::set_status
 * @return array
 */
function bmps_get_booking_statuses() {
	$order_statuses = array(
		'bmps-pending'    => _x( 'Pending payment', 'Booking status', 'woocommerce' ),
		'bmps-processing' => _x( 'Processing', 'Booking status', 'woocommerce' ),
		'bmps-on-hold'    => _x( 'On hold', 'Booking status', 'woocommerce' ),
		'bmps-completed'  => _x( 'Completed', 'Booking status', 'woocommerce' ),
		'bmps-cancelled'  => _x( 'Cancelled', 'Booking status', 'woocommerce' ),
		'bmps-refunded'   => _x( 'Refunded', 'Booking status', 'woocommerce' ),
		'bmps-failed'     => _x( 'Failed', 'Booking status', 'woocommerce' ),
	);
	return apply_filters( 'bmps_booking_statuses', $order_statuses );
}

/**
 * See if a string is an order status.
 * @param  string $maybe_status Status, including any wc- prefix
 * @return bool
 */
function bmps_is_booking_status( $maybe_status ) {
	$order_statuses = bmps_get_booking_statuses();
	return isset( $order_statuses[ $maybe_status ] );
}

/**
 * Get list of statuses which are consider 'paid'.
 * @since  3.0.0
 * @return array
 */
function bmps_get_is_paid_statuses() {
	return apply_filters( 'bmps_booking_is_paid_statuses', array( 'processing', 'completed' ) );
}

/**
 * Get the nice name for an order status.
 *
 * @since  2.2
 * @param  string $status
 * @return string
 */
function bmps_get_booking_status_name( $status ) {
	$statuses = bmps_get_booking_statuses();
	$status   = 'bmps-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
	$status   = isset( $statuses[ 'bmps-' . $status ] ) ? $statuses[ 'bmps-' . $status ] : $status;
	return $status;
}

/**
 * Finds an Order ID based on an order key.
 *
 * @param string $order_key An order key has generated by
 * @return int The ID of an order, or 0 if the order could not be found
 */
function bmps_get_booking_id_by_booking_key( $booking_key ) {
	$data_store = WC_Data_Store::load( 'booking' );
	return $data_store->bmps_get_booking_id_by_booking_key( $booking_key );
}

/**
 * Get all registered order types.
 *
 * $for optionally define what you are getting order types for so only relevant types are returned.
 *
 * e.g. for 'order-meta-boxes', 'order-count'
 *
 * @since  2.2
 * @param  string $for
 * @return array
 */
function bmps_get_booking_types( $for = '' ) {
	global $bmps_booking_types;

	if ( ! is_array( $bmps_booking_types ) ) {
		$bmps_booking_types = array();
	}

	$booking_types = array();

	switch ( $for ) {
		case 'booking-count' :
			foreach ( $bmps_booking_types as $type => $args ) {
				if ( ! $args['exclude_from_booking_count'] ) {
					$booking_types[] = $type;
				}
			}
		break;
		case 'booking-meta-boxes' :
			foreach ( $bmps_booking_types as $type => $args ) {
				if ( $args['add_booking_meta_boxes'] ) {
					$booking_types[] = $type;
				}
			}
		break;
		case 'view-booking' :
			foreach ( $bmps_booking_types as $type => $args ) {
				if ( ! $args['exclude_from_booking_views'] ) {
					$booking_types[] = $type;
				}
			}
		break;
		case 'reports' :
			foreach ( $bmps_booking_types as $type => $args ) {
				if ( ! $args['exclude_from_booking_reports'] ) {
					$booking_types[] = $type;
				}
			}
		break;
		case 'sales-reports' :
			foreach ( $bmps_booking_types as $type => $args ) {
				if ( ! $args['exclude_from_booking_sales_reports'] ) {
					$booking_types[] = $type;
				}
			}
		break;
		default :
			$booking_types = array_keys( $bmps_booking_types );
		break;
	}

	return apply_filters( 'bmps_booking_types', $booking_types, $for );
}

/**
 * Get an order type by post type name.
 * @param  string post type name
 * @return bool|array of datails about the order type
 */
function bmps_get_booking_type( $type ) {
	global $bmps_booking_types;

	if ( isset( $bmps_booking_types[ $type ] ) ) {
		return $bmps_booking_types[ $type ];
	} else {
		return false;
	}
}

/**
 * Register order type. Do not use before init.
 *
 * Wrapper for register post type, as well as a method of telling WC which.
 * post types are types of bookings, and having them treated as such.
 *
 * $args are passed to register_post_type, but there are a few specific to this function:
 *      - exclude_from_bookings_screen (bool) Whether or not this order type also get shown in the main.
 *      bookings screen.
 *      - add_order_meta_boxes (bool) Whether or not the order type gets shop_order meta boxes.
 *      - exclude_from_order_count (bool) Whether or not this order type is excluded from counts.
 *      - exclude_from_order_views (bool) Whether or not this order type is visible by customers when.
 *      viewing bookings e.g. on the my account page.
 *      - exclude_from_order_reports (bool) Whether or not to exclude this type from core reports.
 *      - exclude_from_order_sales_reports (bool) Whether or not to exclude this type from core sales reports.
 *
 * @since  2.2
 * @see    register_post_type for $args used in that function
 * @param  string $type Post type. (max. 20 characters, can not contain capital letters or spaces)
 * @param  array $args An array of arguments.
 * @return bool Success or failure
 */
function bmps_register_booking_type( $type, $args = array() ) {
	if ( post_type_exists( $type ) ) {
		return false;
	}

	global $bmps_booking_types;

	if ( ! is_array( $bmps_booking_types ) ) {
		$bmps_booking_types = array();
	}

	// Register as a post type
	if ( is_wp_error( register_post_type( $type, $args ) ) ) {
		return false;
	}

	// Register for WC usage
	$booking_type_args = array(
		'exclude_from_booking_screen'       => false,
		'add_booking_meta_boxes'             => true,
		'exclude_from_booking_count'         => false,
		'exclude_from_booking_views'         => false,
		'exclude_from_booking_webhooks'      => false,
		'exclude_from_booking_reports'       => false,
		'exclude_from_booking_sales_reports' => false,
		'class_name'                       => 'BMPS_Booking',
	);

	$args                    = array_intersect_key( $args, $booking_type_args );
	$args                    = wp_parse_args( $args, $booking_type_args );
	$bmps_booking_types[ $type ] = $args;

	return true;
}

/**
 * Return the count of processing bookings.
 *
 * @access public
 * @return int
 */
function bmps_processing_booking_count() {
	return bmps_booking_count( 'processing' );
}

/**
 * Return the bookings count of a specific order status.
 *
 * @param string $status
 * @return int
 */
function bmps_booking_count( $status ) {
	$count          = 0;
	$status         = 'bmps-' . $status;
	$order_statuses = array_keys( bmps_get_booking_statuses() );

	if ( ! in_array( $status, $order_statuses ) ) {
		return 0;
	}

	$cache_key = WC_Cache_Helper::get_cache_prefix( 'booking' ) . $status;
	$cached_count = wp_cache_get( $cache_key, 'counts' );

	if ( false !== $cached_count ) {
		return $cached_count;
	}

	foreach ( bmps_get_booking_types( 'order-count' ) as $type ) {
		$data_store = WC_Data_Store::load( 'shop_order' === $type ? 'order' : $type );
		if ( $data_store ) {
			$count += $data_store->get_booking_count( $status );
		}
	}

	wp_cache_set( $cache_key, $count, 'counts' );

	return $count;
}



/**
 * Clear all transients cache for order data.
 *
 * @param int|WC_Order $order
 */
function bmps_delete_booking_transients( $order = 0 ) {
	if ( is_numeric( $order ) ) {
		$order = bmps_get_booking( $order );
	}
	$reports             = WC_Admin_Reports::get_reports();
	$transients_to_clear = array(
		'bmps_admin_report'
	);

	foreach ( $reports as $report_group ) {
		foreach ( $report_group['reports'] as $report_key => $report ) {
			$transients_to_clear[] = 'bmps_report_' . $report_key;
		}
	}

	foreach ( $transients_to_clear as $transient ) {
		delete_transient( $transient );
	}

	// Clear money spent for user associated with order
	if ( is_a( $order, 'WC_Order' ) ) {
		$order_id = $order->get_id();
		delete_user_meta( $order->get_customer_id(), '_money_spent' );
		delete_user_meta( $order->get_customer_id(), '_order_count' );
	} else {
		$order_id = 0;
	}

	// Increments the transient version to invalidate cache
	WC_Cache_Helper::get_transient_version( 'bookings', true );

	// Do the same for regular cache
	WC_Cache_Helper::incr_cache_prefix( 'bookings' );

	do_action( 'woocommerce_delete_shop_order_transients', $order_id );
}

/**
 * See if we only ship to billing addresses.
 * @return bool
 */
function bmps_ship_to_billing_address_only() {
	return 'billing_only' === get_option( 'woocommerce_ship_to_destination' );
}

/**
 * Create a new order refund programmatically.
 *
 * Returns a new refund object on success which can then be used to add additional data.
 *
 * @since 2.2
 * @param array $args
 * @return WC_Order_Refund|WP_Error
 */
function bmps_create_refund( $args = array() ) {
	$default_args = array(
		'amount'         => 0,
		'reason'         => null,
		'order_id'       => 0,
		'refund_id'      => 0,
		'line_items'     => array(),
		'refund_payment' => false,
		'restock_items'  => false,
	);

	try {
		$args = wp_parse_args( $args, $default_args );

		if ( ! $order = bmps_get_booking( $args['order_id'] ) ) {
			throw new Exception( __( 'Invalid order ID.', 'woocommerce' ) );
		}

		$remaining_refund_amount = $order->get_remaining_refund_amount();
		$remaining_refund_items  = $order->get_remaining_refund_items();
		$refund_item_count       = 0;
		$refund                  = new WC_Order_Refund( $args['refund_id'] );

		if ( 0 > $args['amount'] || $args['amount'] > $remaining_refund_amount ) {
			throw new Exception( __( 'Invalid refund amount.', 'woocommerce' ) );
		}

		$refund->set_currency( $order->get_currency() );
		$refund->set_amount( $args['amount'] );
		$refund->set_parent_id( absint( $args['order_id'] ) );
		$refund->set_refunded_by( get_current_user_id() ? get_current_user_id() : 1 );

		if ( ! is_null( $args['reason'] ) ) {
			$refund->set_reason( $args['reason'] );
		}

		// Negative line items
		if ( sizeof( $args['line_items'] ) > 0 ) {
			$items = $order->get_items( array( 'line_item', 'fee', 'shipping' ) );

			foreach ( $items as $item_id => $item ) {
				if ( ! isset( $args['line_items'][ $item_id ] ) ) {
					continue;
				}

				$qty          = isset( $args['line_items'][ $item_id ]['qty'] ) ? $args['line_items'][ $item_id ]['qty'] : 0;
				$refund_total = $args['line_items'][ $item_id ]['refund_total'];
				$refund_tax   = isset( $args['line_items'][ $item_id ]['refund_tax'] ) ? array_filter( (array) $args['line_items'][ $item_id ]['refund_tax'] ) : array();

				if ( empty( $qty ) && empty( $refund_total ) && empty( $args['line_items'][ $item_id ]['refund_tax'] ) ) {
					continue;
				}

				$class         = get_class( $item );
				$refunded_item = new $class( $item );
				$refunded_item->set_id( 0 );
				$refunded_item->add_meta_data( '_refunded_item_id', $item_id, true );
				$refunded_item->set_total( bmps_format_refund_total( $refund_total ) );
				$refunded_item->set_taxes( array( 'total' => array_map( 'bmps_format_refund_total', $refund_tax ), 'subtotal' => array_map( 'bmps_format_refund_total', $refund_tax ) ) );

				if ( is_callable( array( $refunded_item, 'set_subtotal' ) ) ) {
					$refunded_item->set_subtotal( bmps_format_refund_total( $refund_total ) );
				}

				if ( is_callable( array( $refunded_item, 'set_quantity' ) ) ) {
					$refunded_item->set_quantity( $qty * -1 );
				}

				$refund->add_item( $refunded_item );
				$refund_item_count += $qty;
			}
		}

		$refund->update_taxes();
		$refund->calculate_totals( false );
		$refund->set_total( $args['amount'] * -1 );

		/**
		 * Action hook to adjust refund before save.
		 * @since 3.0.0
		 */
		do_action( 'woocommerce_create_refund', $refund, $args );

		if ( $refund->save() ) {
			if ( $args['refund_payment'] ) {
				$result = bmps_refund_payment( $order, $refund->get_amount(), $refund->get_reason() );

				if ( is_wp_error( $result ) ) {
					$refund->delete();
					return $result;
				}
			}

			if ( $args['restock_items'] ) {
				bmps_restock_refunded_items( $order, $args['line_items'] );
			}

			// Trigger notification emails
			if ( ( $remaining_refund_amount - $args['amount'] ) > 0 || ( $order->has_free_item() && ( $remaining_refund_items - $refund_item_count ) > 0 ) ) {
				do_action( 'woocommerce_order_partially_refunded', $order->get_id(), $refund->get_id() );
			} else {
				do_action( 'woocommerce_order_fully_refunded', $order->get_id(), $refund->get_id() );

				$parent_status = apply_filters( 'woocommerce_order_fully_refunded_status', 'refunded', $order->get_id(), $refund->get_id() );

				if ( $parent_status ) {
					$order->update_status( $parent_status );
				}
			}
		}

		do_action( 'woocommerce_refund_created', $refund->get_id(), $args );
		do_action( 'woocommerce_order_refunded', $order->get_id(), $refund->get_id() );

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $refund;
}

/**
 * Try to refund the payment for an order via the gateway.
 *
 * @since 3.0.0
 * @param WC_Order $order
 * @param string $amount
 * @param string $reason
 * @return bool|WP_Error
 */
function bmps_refund_payment( $order, $amount, $reason = '' ) {
	try {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			throw new Exception( __( 'Invalid order.', 'woocommerce' ) );
		}

		$gateway_controller = WC_Payment_Gateways::instance();
		$all_gateways       = $gateway_controller->payment_gateways();
		$payment_method     = $order->get_payment_method();
		$gateway            = isset( $all_gateways[ $payment_method ] ) ? $all_gateways[ $payment_method ] : false;

		if ( ! $gateway ) {
			throw new Exception( __( 'The payment gateway for this order does not exist.', 'woocommerce' ) );
		}

		if ( ! $gateway->supports( 'refunds' ) ) {
			throw new Exception( __( 'The payment gateway for this order does not support automatic refunds.', 'woocommerce' ) );
		}

		$result = $gateway->process_refund( $order->get_id(), $amount, $reason );

		if ( ! $result ) {
			throw new Exception( __( 'An error occurred while attempting to create the refund using the payment gateway API.', 'woocommerce' ) );
		}

		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message() );
		}

		return true;

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}
}

/**
 * Restock items during refund.
 *
 * @since  3.0.0
 * @param  WC_Order $order
 * @param  array $refunded_line_items
 */
function bmps_restock_refunded_items( $order, $refunded_line_items ) {
	$line_items = $order->get_items();

	foreach ( $line_items as $item_id => $item ) {
		if ( ! isset( $refunded_line_items[ $item_id ], $refunded_line_items[ $item_id ]['qty'] ) ) {
			continue;
		}
		$product = $item->get_product();

		if ( $product && $product->managing_stock() ) {
			$old_stock = $product->get_stock_quantity();
			$new_stock = bmps_update_product_stock( $product, $refunded_line_items[ $item_id ]['qty'], 'increase' );

			$order->add_order_note( sprintf( __( 'Item #%1$s stock increased from %2$s to %3$s.', 'woocommerce' ), $product->get_id(), $old_stock, $new_stock ) );

			do_action( 'woocommerce_restock_refunded_item', $product->get_id(), $old_stock, $new_stock, $order, $product );
		}
	}
}

/**
 * Get tax class by tax id.
 *
 * @since 2.2
 * @param int $tax_id
 * @return string
 */
function bmps_get_tax_class_by_tax_id( $tax_id ) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT tax_rate_class FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d", $tax_id ) );
}

/**
 * Get payment gateway class by order data.
 *
 * @since 2.2
 * @param int|WC_Order $order
 * @return WC_Payment_Gateway|bool
 */
function bmps_get_payment_gateway_by_order( $order ) {
	if ( WC()->payment_gateways() ) {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
	} else {
		$payment_gateways = array();
	}

	if ( ! is_object( $order ) ) {
		$order_id = absint( $order );
		$order    = bmps_get_booking( $order_id );
	}

	return is_a( $order, 'WC_Order' ) && isset( $payment_gateways[ $order->get_payment_method() ] ) ? $payment_gateways[ $order->get_payment_method() ] : false;
}

/**
 * When refunding an order, create a refund line item if the partial refunds do not match order total.
 *
 * This is manual; no gateway refund will be performed.
 *
 * @since 2.4
 * @param int $order_id
 */
function bmps_order_fully_refunded( $order_id ) {
	$order       = bmps_get_booking( $order_id );
	$max_refund  = bmps_format_decimal( $order->get_total() - $order->get_total_refunded() );

	if ( ! $max_refund ) {
		return;
	}

	// Create the refund object
	bmps_create_refund( array(
		'amount'     => $max_refund,
		'reason'     => __( 'Order fully refunded', 'woocommerce' ),
		'order_id'   => $order_id,
		'line_items' => array(),
	) );
}
add_action( 'woocommerce_order_status_refunded', 'bmps_order_fully_refunded' );

/**
 * Search bookings.
 *
 * @since  2.6.0
 * @param  string $term Term to search.
 * @return array List of bookings ID.
 */
function bmps_order_search( $term ) {
	$data_store = WC_Data_Store::load( 'order' );
	return $data_store->search_bookings( str_replace( 'Order #', '', bmps_clean( $term ) ) );
}

/**
 * Update total sales amount for each product within a paid order.
 *
 * @since 3.0.0
 * @param int $order_id
 */
function bmps_update_total_sales_counts( $order_id ) {
	$order = bmps_get_booking( $order_id );

	if ( ! $order || $order->get_data_store()->get_recorded_sales( $order ) ) {
		return;
	}

	if ( sizeof( $order->get_items() ) > 0 ) {
		foreach ( $order->get_items() as $item ) {
			if ( $product_id = $item->get_product_id() ) {
				$data_store = WC_Data_Store::load( 'product' );
				$data_store->update_product_sales( $product_id, absint( $item['qty'] ), 'increase' );
			}
		}
	}

	$order->get_data_store()->set_recorded_sales( $order, true );

	/**
	 * Called when sales for an order are recorded
	 *
	 * @param int $order_id order id
	 */
	do_action( 'woocommerce_recorded_sales', $order_id );
}
add_action( 'woocommerce_order_status_completed', 'bmps_update_total_sales_counts' );
add_action( 'woocommerce_order_status_processing', 'bmps_update_total_sales_counts' );
add_action( 'woocommerce_order_status_on-hold', 'bmps_update_total_sales_counts' );

/**
 * Update used coupon amount for each coupon within an order.
 *
 * @since 3.0.0
 * @param int $order_id
 */
function bmps_update_coupon_usage_counts( $order_id ) {
	if ( ! $order = bmps_get_booking( $order_id ) ) {
		return;
	}

	$has_recorded = $order->get_data_store()->get_recorded_coupon_usage_counts( $order );

	if ( $order->has_status( 'cancelled' ) && $has_recorded ) {
		$action = 'reduce';
		$order->get_data_store()->set_recorded_coupon_usage_counts( $order, false );
	} elseif ( ! $order->has_status( 'cancelled' ) && ! $has_recorded ) {
		$action = 'increase';
		$order->get_data_store()->set_recorded_coupon_usage_counts( $order, true );
	} else {
		return;
	}

	if ( sizeof( $order->get_used_coupons() ) > 0 ) {
		foreach ( $order->get_used_coupons() as $code ) {
			if ( ! $code ) {
				continue;
			}

			$coupon = new WC_Coupon( $code );

			if ( ! $used_by = $order->get_user_id() ) {
				$used_by = $order->get_billing_email();
			}

			switch ( $action ) {
				case 'reduce' :
					$coupon->decrease_usage_count( $used_by );
				break;
				case 'increase' :
					$coupon->increase_usage_count( $used_by );
				break;
			}
		}
	}
}
add_action( 'woocommerce_order_status_pending', 'bmps_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_completed', 'bmps_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_processing', 'bmps_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_on-hold', 'bmps_update_coupon_usage_counts' );
add_action( 'woocommerce_order_status_cancelled', 'bmps_update_coupon_usage_counts' );

/**
 * Cancel all unpaid bookings after held duration to prevent stock lock for those products.
 */
function bmps_cancel_unpaid_bookings() {
	$held_duration = get_option( 'woocommerce_hold_stock_minutes' );

	if ( $held_duration < 1 || 'yes' !== get_option( 'woocommerce_manage_stock' ) ) {
		return;
	}

	$data_store    = WC_Data_Store::load( 'order' );
	$unpaid_bookings = $data_store->get_unpaid_bookings( strtotime( '-' . absint( $held_duration ) . ' MINUTES', current_time( 'timestamp' ) ) );

	if ( $unpaid_bookings ) {
		foreach ( $unpaid_bookings as $unpaid_order ) {
			$order = bmps_get_booking( $unpaid_order );

			if ( apply_filters( 'woocommerce_cancel_unpaid_order', 'checkout' === $order->get_created_via(), $order ) ) {
				$order->update_status( 'cancelled', __( 'Unpaid order cancelled - time limit reached.', 'woocommerce' ) );
			}
		}
	}
	wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_bookings' );
	wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 ), 'woocommerce_cancel_unpaid_bookings' );
}
add_action( 'woocommerce_cancel_unpaid_bookings', 'bmps_cancel_unpaid_bookings' );
