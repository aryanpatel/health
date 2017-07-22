<?php
if( !defined('ABSPATH') ){
	exit;
}

class BMPS_Parking_Data_Storage_CPT extends BMPS_Data_Storage_WP implements BMPS_Object_Data_Storage_Interface{
	
	protected $internal_meta_keys = array(
			'_rental_type_daily',
			'_rental_type_monthly',
			'_hourly_price',
			'_daily_price',
			'_monthly_price',
			'_parking_slots',
			'_parking_location',
			'_google_map_link',
			'_parking_features',
			'_parking_suitable_for',
			'_edit_last',
			'_edit_lock',
	);
	
	protected $extra_data_saved = FALSE;
	
	protected $updated_props = array();
	
	public function create(&$data){
		
	}
	
	public function read( &$parking ){
		$parking->set_defaults();
		
		if(! $parking->get_id() || ! ( $post_object = get_post( $parking->get_id() ) ) || 'parking' !== $post_object->post_type){
			throw new Exception(__('Invalid Parking', 'BMPS'));			
		}
		
		$id = $parking->get_id();
		
		$parking->set_props(array(
				'name'	=> $post_object->post_title,
				'slug'	=> $post_object->post_name,
				'date_created'      => 0 < $post_object->post_date_gmt ? bmps_string_to_timestamp( $post_object->post_date_gmt ) : null,
				'date_modified'     => 0 < $post_object->post_modified_gmt ? bmps_string_to_timestamp( $post_object->post_modified_gmt ) : null,
				'status'            => $post_object->post_status,
				'description'       => $post_object->post_content,
				//'short_description' => $post_object->post_excerpt,
				//'parent_id'         => $post_object->post_parent,
				//'menu_order'        => $post_object->menu_order,
				'reviews_allowed'   => 'open' === $post_object->comment_status,
		));
		
		//$this->read_parking_data($parking);
		$this->read_extra_data($parking);exit;
	}
	
	public function update(&$parking){
		
	}
	
	public function delete( &$parking, $args = array() ) {
		
	}
	
	protected function read_parking_data( &$parking) {
		$id = $parking->get_id();
		
		$parking->set_props( array(
				'rental_type_daily' 	=> get_post_meta($id, '_rental_type_daily', TRUE),
				'hourly_price' 			=> get_post_meta($id, '_hourly_price', TRUE),
				'daily_price' 			=> get_post_meta($id, '_daily_price', TRUE),
				'rental_type_monthly' 	=> get_post_meta($id, '_rental_type_monthly', TRUE),
				'monthly_price' 		=> get_post_meta($id, '_monthly_price', TRUE),
				'parking_slots' 		=> get_post_meta($id, '_parking_slots', TRUE),
				'parking_location'		=> get_post_meta($id, '_parking_location', TRUE),
				'google_map_link'		=> get_post_meta($id, '_google_map_link', TRUE),
				'parking_features'		=> get_post_meta($id,'_parking_features', TRUE),
				'parking_suitable_for'	=> get_post_meta($id, '_parking_suitable_for', TRUE),
				'image_id'				=> get_post_thumbnail_id($id)
		) );
	}
	
	protected function read_extra_data( &$parking) {
		echo "<prE>";
		print_r($parking->get_extra_data_keys());exit;
		//foreach ($parking->get_extra_data_keys())
	}
	
	protected function update_post_meta( &$parking, $force = false ) {
		
	}
	
	protected function handle_updated_props( &$parking) {
		
	}
	
	/**
	 * For all stored terms in all taxonomies, save them to the DB.
	 *
	 * @param WC_Product
	 * @param bool Force update. Used during create.
	 * @since 3.0.0
	 */
	protected function update_terms( &$product, $force = false ) {

	}
	
	/**
	 * Update visibility terms based on props.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Product $product
	 * @param bool $force Force update. Used during create.
	 */
	protected function update_visibility( &$product, $force = false ) {
	}
	
	/**
	 * Update attributes which are a mix of terms and meta data.
	 *
	 * @param WC_Product
	 * @param bool Force update. Used during create.
	 * @since 3.0.0
	 */
	protected function update_attributes( &$product, $force = false ) {
	}
	
	/**
	 * Update downloads.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product
	 * @param bool Force update. Used during create.
	 * @return bool If updated or not.
	 */
	protected function update_downloads( &$product, $force = false ) {
	}
	
	/**
	 * Make sure we store the product type and version (to track data changes).
	 *
	 * @param WC_Product
	 * @since 3.0.0
	 */
	protected function update_version_and_type( &$product ) {
	}
	
	/**
	 * Clear any caches.
	 *
	 * @param WC_Product
	 * @since 3.0.0
	 */
	protected function clear_caches( &$product ) {
	}
	
	/*
	 |--------------------------------------------------------------------------
	 | wc-product-functions.php methods
	 |--------------------------------------------------------------------------
	 */
	
	/**
	 * Returns an array of on sale products, as an array of objects with an
	 * ID and parent_id present. Example: $return[0]->id, $return[0]->parent_id.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function get_on_sale_products() {
	}
	
	/**
	 * Returns a list of product IDs ( id as key => parent as value) that are
	 * featured. Uses get_posts instead of wc_get_products since we want
	 * some extra meta queries and ALL products (posts_per_page = -1).
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function get_featured_product_ids() {
	}
	
	/**
	 * Check if product sku is found for any other product IDs.
	 *
	 * @since 3.0.0
	 * @param int $product_id
	 * @param string $sku Will be slashed to work around https://core.trac.wordpress.org/ticket/27421
	 * @return bool
	 */
	public function is_existing_sku( $product_id, $sku ) {
	}
	
	/**
	 * Return product ID based on SKU.
	 *
	 * @since 3.0.0
	 * @param string $sku
	 * @return int
	 */
	public function get_product_id_by_sku( $sku ) {
	}
	
	/**
	 * Returns an array of IDs of products that have sales starting soon.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_starting_sales() {
	}
	
	/**
	 * Returns an array of IDs of products that have sales which are due to end.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_ending_sales() {
	}
	
	/**
	 * Find a matching (enabled) variation within a variable product.
	 *
	 * @since  3.0.0
	 * @param  WC_Product $product Variable product.
	 * @param  array $match_attributes Array of attributes we want to try to match.
	 * @return int Matching variation ID or 0.
	 */
	public function find_matching_product_variation( $product, $match_attributes = array() ) {
	
	}
	
	/**
	 * Make sure all variations have a sort order set so they can be reordered correctly.
	 *
	 * @param int $parent_id
	 */
	public function sort_all_product_variations( $parent_id ) {
	
	}
	
	/**
	 * Return a list of related products (using data like categories and IDs).
	 *
	 * @since 3.0.0
	 * @param array $cats_array  List of categories IDs.
	 * @param array $tags_array  List of tags IDs.
	 * @param array $exclude_ids Excluded IDs.
	 * @param int   $limit       Limit of results.
	 * @param int   $product_id
	 * @return array
	 */
	public function get_related_products( $cats_array, $tags_array, $exclude_ids, $limit, $product_id ) {
		}
	
	/**
	 * Builds the related posts query.
	 *
	 * @since 3.0.0
	 *
	 * @param array $cats_array  List of categories IDs.
	 * @param array $tags_array  List of tags IDs.
	 * @param array $exclude_ids Excluded IDs.
	 * @param int   $limit       Limit of results.
	 *
	 * @return array
	 */
	public function get_related_products_query( $cats_array, $tags_array, $exclude_ids, $limit ) {
	}
	
	/**
	 * Update a product's stock amount directly.
	 *
	 * Uses queries rather than update_post_meta so we can do this in one query (to avoid stock issues).
	 *
	 * @since  3.0.0 this supports set, increase and decrease.
	 * @param  int
	 * @param  int|null $stock_quantity
	 * @param  string $operation set, increase and decrease.
	 */
	public function update_product_stock( $product_id_with_stock, $stock_quantity = null, $operation = 'set' ) {
	}
	
	/**
	 * Update a product's sale count directly.
	 *
	 * Uses queries rather than update_post_meta so we can do this in one query for performance.
	 *
	 * @since  3.0.0 this supports set, increase and decrease.
	 * @param  int
	 * @param  int|null $quantity
	 * @param  string $operation set, increase and decrease.
	 */
	public function update_product_sales( $product_id, $quantity = null, $operation = 'set' ) {
	}
	
	/**
	 * Update a products average rating meta.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product
	 */
	public function update_average_rating( $product ) {
	}
	
	/**
	 * Update a products review count meta.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product
	 */
	public function update_review_count( $product ) {
	}
	
	/**
	 * Update a products rating counts.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product
	 */
	public function update_rating_counts( $product ) {
	}
	
	/**
	 * Get shipping class ID by slug.
	 *
	 * @since 3.0.0
	 * @param $slug string
	 * @return int|false
	 */
	public function get_shipping_class_id_by_slug( $slug ) {
	}
	
	/**
	 * Returns an array of products.
	 *
	 * @param  array $args @see wc_get_products
	 *
	 * @return array|object
	 */
	public function get_products( $args = array() ) {
	}
	
	/**
	 * Search product data for a term and return ids.
	 *
	 * @param  string $term
	 * @param  string $type of product
	 * @param  bool $include_variations in search or not
	 * @return array of ids
	 */
	public function search_products( $term, $type = '', $include_variations = false ) {
	}
	
	/**
	 * Get the product type based on product ID.
	 *
	 * @since 3.0.0
	 * @param int $product_id
	 * @return bool|string
	 */
	public function get_product_type( $product_id ) {
	}
}