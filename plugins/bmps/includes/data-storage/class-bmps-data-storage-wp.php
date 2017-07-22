<?php
if (! defined ( 'ABSPATH' )) {
	exit ();
}
class BMPS_Data_Storage_WP {
	
	/**
	 * Meta type.
	 * This should match up with
	 * the types available at https://codex.wordpress.org/Function_Reference/add_metadata.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 */
	protected $meta_type = 'post';
	
	/**
	 * Requires if using custom metadata types
	 *
	 * @var string
	 */
	protected $object_id_field_for_meta = '';
	
	/**
	 * Data stored in meta keys, but not considered "meta" for an object.
	 *
	 * @var array
	 */
	protected $internal_meta_keys = array ();
	
	
	public function read_meta( &$object ) {
		global $wpdb;
		$db_info 		= $this->get_db_info();
		$query			= "SELECT {$db_info['meta_id_field']} as meta_id, meta_key, meta_value FROM {$db_info['table']} WHERE {$db_info['object_id_field']} = %d ORDER BY {$db_info['meta_id_field']}";
		$row_meta_data 	= $wpdb->get_results($wpdb->prepare($query, $object->get_id()));
		
		$this->internal_meta_keys	= array_merge(array_map( array($this, 'prefix_key'), $object->get_data_keys() ), $this->internal_meta_keys);
		return array_filter($row_meta_data, array($this, 'exclude_internal_meta_keys'));
	}
	/**
	 * Table structure is slightly different between meta types, this function will return what we need to know.
	 *
	 * @return array Array elements: table, object_id_field, meta_id_field
	 */
	protected function get_db_info() {
		global $wpdb;
		
		$meta_id_field = 'meta_id';
		$table = $wpdb->prefix;
		
		//If this is not metadata of core post type, prefix will be added
		if (! in_array ( $this->meta_type, array (
				'post',
				'user',
				'comment',
				'term' 
		) )) {
			$table .= 'bmps_';
		}
		
		$table .= $this->meta_type . 'meta';
		$object_id_field = $this->meta_type . '_id';
		
		// Figure out our field names.
		if ( 'user' === $this->meta_type ) {
			$meta_id_field = 'umeta_id';
			$table         = $wpdb->usermeta;
		}
		
		if ( ! empty( $this->object_id_field_for_meta ) ) {
			$object_id_field = $this->object_id_field_for_meta;
		}
		
		return array(
				'table'           => $table,
				'object_id_field' => $object_id_field,
				'meta_id_field'   => $meta_id_field,
		);
	}
	
	/**
	 * Deletes meta based on meta ID.
	 *
	 * @since  3.0.0
	 * @param  WC_Data
	 * @param  stdClass (containing at least ->id)
	 * @return array
	 */
	public function delete_meta( &$object, $meta ) {
		delete_metadata_by_mid( $this->meta_type, $meta->id );
	}
	
	/**
	 * Add new piece of meta.
	 *
	 * @since  3.0.0
	 * @param  WC_Data
	 * @param  stdClass (containing ->key and ->value)
	 * @return int meta ID
	 */
	public function add_meta( &$object, $meta ) {
		return add_metadata( $this->meta_type, $object->get_id(), $meta->key, wp_slash( $meta->value ), false );
	}
	
	/**
	 * Update meta.
	 *
	 * @since  3.0.0
	 * @param  WC_Data
	 * @param  stdClass (containing ->id, ->key and ->value)
	 */
	public function update_meta( &$object, $meta ) {
		update_metadata_by_mid( $this->meta_type, $meta->id, wp_slash( $meta->value ), $meta->key );
	}
	
	/**
	 * Internal meta keys we don't want exposed as part of meta_data. This is in
	 * addition to all data props with _ prefix.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	protected function prefix_key( $key ) {
		return '_' === substr( $key, 0, 1 ) ? $key : '_' . $key;
	}
	
	/**
	 * Callback to remove unwanted meta data.
	 *
	 * @param object $meta
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return ! in_array( $meta->meta_key, $this->internal_meta_keys ) && 0 !== stripos( $meta->meta_key, 'wp_' );
	}
}
?>