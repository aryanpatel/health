<?php

if( !defined('ABSPATH') ){
	exit;
}


class BMPS_Data_Storage {
	
	private $instance = NULL;
	
	private $storages = array(
			'parking'	=> 'BMPS_Parking_Data_Storage_CPT'
	);
	
	private $current_class_name;
	
	private $object_type;
	
	public function __construct( $object_type ){
		$this->object_type = $object_type;
		$this->storages = apply_filters('bmps_data_storage', $this->storages);
		
		//If object type does not found, check one leval up
		if( !array_key_exists($object_type, $this->storages) ){
			$pieces = explode('-', $object_type);
			$object_type = $pieces[0];
		}
		
		if( array_key_exists($object_type, $this->storages) ){
			$storage = apply_filters('bmps_' . $object_type . '_data_storage', $this->storages[$object_type]);
			if( is_object($storage) ){
				if( ! $storage instanceof BMPS_Object_Data_Storage_Interface ){
					throw new Exception(__('Invalid data storage', 'BMPS'));
				}
				$this->current_class_name = get_class($storage);
				$this->instance			  = $storage;
			} else {
				if( !class_exists($storage) ) {
					throw new Exception(__('Invalid data storage','BMPS'));
				}
				$this->current_class_name = $storage;
				$this->instance			  = new $storage;
			}

		} else {
			throw new Exception(__('Invalid data storage','BMPS'));
		}
	}
	/**
	 * Only store the object type to avoid serializing the data store instance.
	 *
	 * @return array
	 */
	public function __sleep(){
		return array('object_type');
	}
	
	/**
	 * Re-run the constructor with the object type.
	 */
	public function __wakeup(){
		$this->__construct($this->object_type);
	}
	
	/**
	 * Loads a data storage
	 * 
	 * @param string $object_type Name of object type
	 * @return BMPS_Data_Storage
	 */
	
	public static function load($object_type){
		return new BMPS_Data_Storage($object_type);
	}
	
	/**
	 * Returns current data storage class name
	 * 
	 * @return string
	 */
	public function get_current_class_name(){
		return $this->current_class_name;
	}
	
	/**
	 * Read an object from data storage
	 * 
	 * @param BMPS_Data
	 */
	public function read(&$data){
		$this->instance->read($data);
	}
	
	/**
	 * Create an object in data storage
	 * 
	 * @param BMPS_Data
	 */
	
	public function create(&$data){
		$this->instance->create($data);
	}
	
	/**
	 * Update an object in the data storages
	 * @param BMPS_Data
	 */
	public function update(&$data){
		$this->instance->update($data);
	}
	
	/**
	 * Delete an object from data storage
	 * 
	 * @param BMPS_Data
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete(&$data){
		$this->instance->delete($data);
	}
	
	/**
	 * Data storges can define additional functions (for example, coupons have
	 * some helper methods for increasing or decreasing usage). This passes
	 * through to the instance if that function exists.
	 *
	 *
	 * @param $method
	 * @param $parameters
	 *
	 * @return mixed
	 */
	
	public function __call($method, $parameters){
		if(is_callable(array($this->instance, $method))) {
			$object = array_shift($parameters);
			return call_user_func_array(array($this->instance,$method), array(&$object), $parameters);
		}
	}
}