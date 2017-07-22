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
class BMPS_Parking extends BMPS_Parking_Base {
	
	public function __construct( $parking = 0 ){
		parent::__construct($parking);
	}
	
	/**
	 * Get internal type.
	 * @return string
	 */
	public function get_type() {
		return 'simple';
	}
}
