<?php
if (! defined ( 'ABSPATH' )) {
	exit ();
}

abstract class BMPS_Data{
	/**
	 * ID for this object
	 *
	 * @var integer
	 */
	protected $id = 0;
	
	/**
	 * Name + value paired data for this object
	 *
	 * @var array
	 */
	protected $data = array ();
	
	/**
	 * Core data changes for this object.
	 *
	 * @var array
	 */
	protected $changes = array ();
	
	/**
	 * Extra data for this object.
	 * Name value pairs (name + default value).
	 * Used as a standard way for sub classes (like parkings types) to add
	 * additional information to an inherited class.
	 *
	 * @var array
	 */
	protected $extra_data = array ();
	
	/**
	 * To track and reset if needed, we will set _data in construct
	 *
	 * @var array
	 */
	protected $default_data = array ();
	
	/**
	 * Refrence to the data storage for this class
	 *
	 * @var string
	 */
	protected $data_storage;
	
	/**
	 * Once object has been read form DataBase, this will set to true, untill then
	 * This will be false
	 *
	 * @var bool
	 */
	protected $object_read = false;
	
	/**
	 * This is the name of this object type.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	protected $object_type = 'data';
	
	/**
	 * Stores additional meta data
	 *
	 * @var array
	 */
	protected $meta_data = NULL;
	
	/**
	 * Default constructer for class
	 *
	 * @param int|object|array $read
	 *        	ID to load from the DB (optional) or already queried data.
	 */
	public function __construct($read = 0) {
		$this->data = array_merge ( $this->data, $this->extra_data );
		$this->default_data = $this->data;
	}
	
	/**
	 * To avoid serializing the data object instance, store only object ID
	 *
	 * @return array
	 */
	public function __sleep() {
		return array (
				'id'
		);
	}
	public function __wakeup() {
		try {
			$this->__construct ( absint ( $this->id ) );
		} catch ( Exception $e ) {
			$this->set_id( 0 );
			$this->set_object_read( true );
		}
	}
	
	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 */
	public function __clone() {
		$this->maybe_read_meta_data();
		if ( ! empty( $this->meta_data ) ) {
			foreach ( $this->meta_data as $array_key => $meta ) {
				$this->meta_data[ $array_key ] = clone $meta;
				if ( ! empty( $meta->id ) ) {
					unset( $this->meta_data[ $array_key ]->id );
				}
			}
		}
	}
	
	/**
	 * Change data to JSON format.
	 *
	 * @since  2.6.0
	 * @return string Data in JSON format.
	 */
	public function __toString() {
		return json_encode( $this->get_data() );
	}
	
	/**
	 * Returns all data for this object.
	 *
	 * @since  2.6.0
	 * @return array
	 */
	public function get_data() {
		return array_merge( array( 'id' => $this->get_id() ), $this->data, array( 'meta_data' => $this->get_meta_data() ) );
	}
	
	/**
	 * Returns array of expected data keys for this object.
	 *
	 * @since   3.0.0
	 * @return array
	 */
	public function get_data_keys() {
		return array_keys( $this->data );
	}
	
	/**
	 * Returns all "extra" data keys for an object (for sub objects like product types).
	 *
	 * @since  3.0.0
	 * @return array
	 */
	public function get_extra_data_keys() {
		return array_keys( $this->extra_data );
	}
	
	/**
	 * See if meta data exists, since get_meta always returns a '' or array().
	 *
	 * @since  3.0.0
	 * @param  string $key
	 * @return boolean
	 */
	public function meta_exists( $key = '' ) {
		$this->maybe_read_meta_data();
		$array_keys = wp_list_pluck( $this->get_meta_data(), 'key' );
		return in_array( $key, $array_keys );
	}
	
	/**
	 * Add meta data.
	 *
	 * @since 2.6.0
	 * @param string $key Meta key
	 * @param string $value Meta value
	 * @param bool $unique Should this be a unique key?
	 */
	public function add_meta_data( $key, $value, $unique = false ) {
		$this->maybe_read_meta_data();
		if ( $unique ) {
			$this->delete_meta_data( $key );
		}
		$this->meta_data[] = (object) array(
				'key'   => $key,
				'value' => $value,
		);
	}
	
	
	
	/**
	 * Read meta data if null
	 */
	protected function maybe_read_meta_data() {
		if ( is_null( $this->meta_data ) ) {
			$this->read_meta_data();
		}
	}
	public function read_meta_data($force_read = FALSE) {
		$this->meta_data = array();
		echo "<prE>";
		print_r($this);exit;
		if (! $this->get_id ()) {
			return;
		}
		if (! $this->data_storage) {
			return;
		}
		$row_meta_data = $this->data_storage->read_meta( $this );
		if ( $raw_meta_data ) {
			foreach ( $raw_meta_data as $meta ) {
				$this->meta_data[] = (object) array(
						'id'    => (int) $meta->meta_id,
						'key'   => $meta->meta_key,
						'value' => maybe_unserialize( $meta->meta_value ),
				);
			}
		}
	}
	
	/**
	 * get meta data
	 *
	 * @return array
	 */
	public function get_meta_data() {
		$this->maybe_read_meta_data ();
		return array_filter ( $this->meta_data, array (
				$this,
				'filter_null_meta'
		) );
	}
	
	/**
	 * Filter null meta values from array.
	 *
	 *
	 * @param mixed $meta
	 *
	 * @return bool
	 */
	protected function filter_null_meta($meta) {
		return ! is_null ( $meta->value );
	}
	
	/**
	 * Get Meta Data by Key.
	 *
	 * @since 2.6.0
	 * @param string $key
	 * @param bool $single
	 *        	return first found meta with key, or all with $key
	 * @param string $context
	 *        	What the value is for. Valid values are view and edit.
	 * @return mixed
	 */
	public function get_meta($key = '', $single = true, $context = 'view') {
		$this->maybe_read_meta_data();
		$meta_data = $this->get_meta_data();
		$array_keys = array_keys ( wp_list_pluck ( $meta_data, 'key' ), $key );
		$value = $single ? '' : array ();
		
		if (! empty ( $array_keys )) {
			// We don't use the $this->meta_data property directly here because we don't want meta with a null value (i.e. meta which has been deleted via $this->delete_meta_data())
			if ($single) {
				$value = $meta_data [current ( $array_keys )]->value;
			} else {
				$value = array_intersect_key ( $meta_data, array_flip ( $array_keys ) );
			}
			
			if ('view' === $context) {
				$value = apply_filters ( $this->get_hook_prefix () . $key, $value, $this );
			}
		}
		
		return $value;
	}
	
	/**
	 * Update Meta Data in database
	 */
	public function save_meta_data() {
		echo "<pre>";
		print_r($this);exit;
		if (! $this->data_storage || is_null ( $this->meta_data )) {
			return;
		}
		foreach ( $this->meta_data as $array_key => $meta ) {
			if (is_null ( $meta->value )) {
				if (! empty ( $meta->id )) {
					$this->data_storage->delete_meta ( $this, $meta );
					unset ( $this->meta_data [$array_key] );
				}
			} elseif (empty ( $meta->id )) {
				$new_meta_id = $this->data_storage->add_meta ( $this, $meta );
				$this->meta_data [$array_key]->id = $new_meta_id;
			} else {
				$this->data_storage->update_meta ( $this, $meta );
			}
		}
	}
	
	/**
	 * Set ID
	 *
	 * @param int $id
	 */
	public function set_id($id) {
		$this->id = absint ( $id );
	}
	
	/**
	 * Set all props to default values.
	 *
	 * @since 3.0.0
	 */
	public function set_defaults() {
		$this->data        = $this->default_data;
		$this->changes     = array();
		$this->set_object_read( false );
 	}
	
	/**
	 * Set all meta data from array.
	 *
	 * @since 2.6.0
	 * @param array $data Key/Value pairs
	 */
	public function set_meta_data( $data ) {
		if ( ! empty( $data ) && is_array( $data ) ) {
			$this->maybe_read_meta_data();
			foreach ( $data as $meta ) {
				$meta = (array) $meta;
				if ( isset( $meta['key'], $meta['value'], $meta['id'] ) ) {
					$this->meta_data[] = (object) array(
							'id'    => $meta['id'],
							'key'   => $meta['key'],
							'value' => $meta['value'],
					);
				}
			}
		}
	}
	
	/**
	 * Get ID
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}
	
	/**
	 * Delete an object, set the ID to 0, and return result.
	 *
	 * @since  2.6.0
	 * @param  bool $force_delete
	 * @return bool result
	 */
	public function delete( $force_delete = false ) {
		if ( $this->data_storage ) {
			$this->data_storage->delete( $this, array( 'force_delete' => $force_delete ) );
			$this->set_id( 0 );
			return true;
		}
		return false;
	}
	
	/**
	 * Delete meta data.
	 *
	 * @since 2.6.0
	 * @param string $key Meta key
	 */
	public function delete_meta_data( $key ) {
		$this->maybe_read_meta_data();
		if ( $array_keys = array_keys( wp_list_pluck( $this->meta_data, 'key' ), $key ) ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}
	
	/**
	 * Delete meta data.
	 *
	 * @since 2.6.0
	 * @param int $mid Meta ID
	 */
	public function delete_meta_data_by_mid( $mid ) {
		$this->maybe_read_meta_data();
		if ( $array_keys = array_keys( wp_list_pluck( $this->meta_data, 'id' ), $mid ) ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}
	
	/**
	 * Set object read
	 *
	 * @param bool $read
	 */
	public function set_object_read($read = TRUE) {
		$this->object_read = ( bool ) $read;
	}
	
	/**
	 * Get the data storage.
	 *
	 * @return object
	 */
	public function get_data_storage() {
		return $this->data_storage;
	}
	
	/**
	 * Get object read property.
	 *
	 * @since  3.0.0
	 * @return boolean
	 */
	public function get_object_read() {
		return (bool) $this->object_read;
	}
	
	public function set_porps($properties, $context = 'set') {
		$errors = new WP_Error ();
		echo "<prE>";
		print_r($properties);exit;
		foreach ( $properties as $propertie => $value ) {
			
			try {
				if ('meta_data' === $propertie) {
					continue;
				}
				$setter = "set_$propertie";
				if (! is_null ( $value ) && is_callable ( array (
						$this,
						$setter
				) )) {
					$reflex = new ReflectionMethod ( $this, $setter );
					
					if ($reflex->isPublic ()) {
						$this->{$setter} ( $value );
					}
				}
			} catch ( BMPS_Data_Exception $e ) {
				$errors->add ( $e->getErrorCode (), $e->getErrorData () );
			}
		}
	}
	
	/**
	 * Update meta data by key or ID, if provided.
	 * @since  2.6.0
	 *
	 * @param  string $key
	 * @param  string $value
	 * @param  int $meta_id
	 */
	public function update_meta_data( $key, $value, $meta_id = '' ) {
		$this->maybe_read_meta_data();
		if ( $array_key = $meta_id ? array_keys( wp_list_pluck( $this->meta_data, 'id' ), $meta_id ) : '' ) {
			$this->meta_data[ current( $array_key ) ] = (object) array(
					'id'    => $meta_id,
					'key'   => $key,
					'value' => $value,
			);
		} else {
			$this->add_meta_data( $key, $value, true );
		}
	}
	
	/**
	 * Sets a prop for a setter method.
	 *
	 * This stores changes in a special array so we can track what needs saving
	 * the the DB later.
	 *
	 * @since 3.0.0
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 */
	protected function set_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
					$this->changes[ $prop ] = $value;
				}
			} else {
				$this->data[ $prop ] = $value;
			}
		}
	}
	
	/**
	 * Gets a prop for a getter method.
	 *
	 * Gets the value from either current pending changes, or the data itself.
	 * Context controls what happens to the value before it's returned.
	 *
	 * @param string $prop
	 *        	Name of prop to get.
	 * @param string $context
	 *        	What the value is for. Valid values are view and edit.
	 * @return mixed
	 */
	protected function get_prop($prop, $context = 'view') {
		$value = null;
		
		if (array_key_exists ( $prop, $this->data )) {
			$value = array_key_exists ( $prop, $this->changes ) ? $this->changes [$prop] : $this->data [$prop];
			
			if ('view' === $context) {
				$value = apply_filters ( $this->get_hook_prefix () . $prop, $value, $this );
			}
		}
		
		return $value;
	}
	
	/**
	 * Return data changes only.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_changes() {
		return $this->changes;
	}
	
	/**
	 * Merge changes with data and clear.
	 *
	 * @since 3.0.0
	 */
	public function apply_changes() {
		$this->data    = array_replace_recursive( $this->data, $this->changes );
		$this->changes = array();
	}
	
	/**
	 * Prefix for action and filter hooks on data.
	 *
	 * @return string
	 */
	protected function get_hook_prefix() {
		return 'bmps_' . $this->object_type . '_get_';
	}
	
	/**
	 * Sets a date prop whilst handling formatting and datatime objects.
	 *
	 * @since 3.0.0
	 * @param string $prop Name of prop to set.
	 * @param string|integer $value Value of the prop.
	 */
	protected function set_date_prop( $prop, $value ) {
		try {
			if ( empty( $value ) ) {
				$this->set_prop( $prop, null );
				return;
			}
			
			if ( is_a( $value, 'BMPS_DateTime' ) ) {
				$datetime = $value;
			} elseif ( is_numeric( $value ) ) {
				// Timestamps are handled as UTC timestamps in all cases.
				$datetime = new WC_DateTime( "@{$value}", new DateTimeZone( 'UTC' ) );
			} else {
				// Strings are defined in local WP timezone. Convert to UTC.
				if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits ) ) {
					$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : wc_timezone_offset();
					$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
				} else {
					$timestamp = bmps_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', bmps_string_to_timestamp( $value ) ) ) );
				}
				$datetime  = new BMPS_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
			}
			
			// Set local timezone or offset.
			if ( get_option( 'timezone_string' ) ) {
				$datetime->setTimezone( new DateTimeZone( bmps_timezone_string() ) );
			} else {
				$datetime->set_utc_offset( bmps_timezone_offset() );
			}
			
			$this->set_prop( $prop, $datetime );
		} catch ( Exception $e ) {}
	}
	
	/**
	 * When invalid data is found, throw an exception unless reading from the DB.
	 *
	 * @throws WC_Data_Exception
	 * @since 3.0.0
	 * @param string $code             Error code.
	 * @param string $message          Error message.
	 * @param int    $http_status_code HTTP status code.
	 * @param array  $data             Extra error data.
	 */
	protected function error( $code, $message, $http_status_code = 400, $data = array() ) {
		throw new BMPS_Data_Exception( $code, $message, $http_status_code, $data );
	}
}