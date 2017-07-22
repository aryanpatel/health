<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface BMPS_Object_Data_Storage_Interface{
	
	/**
	 * Method to create a new record of a BMPS_Data based object
	 * 
	 * @param BMPS_data &$data
	 */
	public function create(&$data);
	
	/**
	 * Method to read data. Create new BMPS_Data based object
	 * @param unknown $data
	 */
	public function read(&$data);
	
	/**
	 * Updates a record in the database.
	 * @param BMPS_Data &$data
	 */
	public function update( &$data );
	
	/**
	 * Deletes a record from the database.
	 * @param  BMPS_Data &$data
	 * @param  array $args Array of args to pass to the delete method.
	 * @return bool result
	 */
	public function delete( &$data, $args = array() );
	
	/**
	 * Returns an array of meta for an object.
	 * @param  BMPS_Data &$data
	 * @return array
	 */
	public function read_meta( &$data );
	
	/**
	 * Deletes meta based on meta ID.
	 * @param  BMPS_Data &$data
	 * @param  object $meta (containing at least ->id)
	 * @return array
	 */
	public function delete_meta( &$data, $meta );
	
	/**
	 * Add new piece of meta.
	 * @param  BMPS_Data &$data
	 * @param  object $meta (containing ->key and ->value)
	 * @return int meta ID
	 */
	public function add_meta( &$data, $meta );
	
	/**
	 * Update meta.
	 * @param  BMPS_Data &$data
	 * @param  object $meta (containing ->id, ->key and ->value)
	 */
	public function update_meta( &$data, $meta );
}