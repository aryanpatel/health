<?php
/**
 * Book My Parking Space Data Exception Class
 * 
 * This class will extend PHP exception to provide more detailed data
 * 
 * @author Krutarth Patel <krutarth@sourcefragment.com>
 * @category Core
 * @package BMPS
 * @since	1.0
 *
 */

if( !defined( 'ABSPATH' ) ){
	exit;
}

class BMPS_Data_Exception extends Exception{
	
	/**
	 * Sanitized error code.
	 * 
	 * @var string
	 */
	
	protected $error_code;
	
	/**
	 * Extra information on error
	 * 
	 * @var array
	 */
	
	protected $error_data;
	
	/**
	 * 
	 * Exception construct class
	 * 
	 * @param string $code 			non-human readble error code e.g. 'invalid_post_id'
	 * @param string $message		translated error message
	 * @param int $status_code		Error code e.g. 404
	 * @param array $data			Extra information on error
	 */
	
	public function __construct($code, $message, $status_code, $data=array()){
		$this->error_code = $code;
		$this->error_data = array_merge(array('status' => $status_code), $data);
		
		parent::__construct($message,$status_code);
	}
	
	/**
	 * Get Error code
	 * 
	 * @return string
	 */
	
	public function getErrorCode(){
		return $this->error_code;
	}
	
	/**
	 * Get Error Data
	 * 
	 * @return array
	 */
	
	public function getErrorData(){
		return $this->error_data;
	}
}