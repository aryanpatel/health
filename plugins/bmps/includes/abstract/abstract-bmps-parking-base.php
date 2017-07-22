<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}

/**
 * Parking class
 *
 * The class used to save parking data
 *
 * @class BMPS_Parking
 * 
 * @category Class
 * @author Krutarth Pate <krutarth.patel7@gmail.com>
 */
class BMPS_Parking_Base extends BMPS_Data {
	
	protected $object_type = 'parking';
	
	/**
	 * Post type.
	 * 
	 * @var string
	 */
	protected $post_type = 'parking';
	
	/**
	 * Stores Parking Data
	 *
	 * @var array
	 */
	protected $data = array (
			'name' => '',
			'slug' => '',
			'date_created' => null,
			'date_modified' => null,
			'status' => FALSE,
			'rental_type_daily' => TRUE,
			'rental_type_monthly' => FALSE,
			'hourly_price' => '',
			'daily_price' => '',
			'monthly_price' => '',
			'parking_slots' => 0,
			'parking_location' => '',
			'google_map_link' => '',
			'parking_features' => '',
			'parking_suitable_for' => '' 
	);
	public function __construct($parking = 0) {
		if (is_numeric ( $parking ) && $parking > 0) {
			$this->set_id ( $parking );
		} else if ($parking instanceof self) {
			$this->set_id ( absint ( $this->get_id () ) );
		} else if (! empty ( $parking->ID )) {
			$this->set_id ( absint ( $parking->ID ) );
		} else {
			$this->set_object_read ( TRUE );
		}
		$this->data_storage = BMPS_Data_Storage::load($this->post_type);
		if( $this->get_id() > 0 ) {
			$this->data_storage->read($this);
		}
	}
	
	/**
	 * Save Data (either create or update)
	 */
	public function save() {
		if( $this->data_storage ) {
			do_action('bmps_before_' . $this->object_type . '_object_save', $this, $this->data_storage);
			if( $this->get_id() ) {
				$this->data_storage->update($this);
			} else {
				$this->data_storage->create($this);
			}
			if( $this->get_parent_id() ) {
				bmps_deferred_parking_sync( $this->get_parent_id() );
			}
			exit;
			return $this->get_id();
		}
	}
	
	/**
	 * Get internal type. Should return string and *should be overridden* by child classes.
	 *
	 * The product_type property is deprecated but is used here for BW compat with child classes which may be defining product_type and not have a get_type method.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_type() {
		return isset( $this->parking_type ) ? $this->parking_type: 'simple';
	}
	
	/**
	 * Return the unique ID for this object
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}
	
	
	/**
	 * Get parent ID.
	 *
	 * @param  string $context
	 * @return int
	 */
	
	public function get_parent_id( $context = 'view' ) {
		return $this->get_prop('parent_id', $context);
	}
	
	/**
	 * Get parking created date.
	 *
	 * @param  string $context
	 * @return BMPS_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}
}