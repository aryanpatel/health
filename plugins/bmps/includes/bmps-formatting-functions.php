<?php 

if( !defined('ABSPATH') ){
	exit;
}

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
 *
 * @param string $time_string
 * @param int|null $from_timestamp
 *
 * @return int
 */
function bmps_string_to_timestamp( $time_string, $from_timestamp = null ) {
	$original_timezone = date_default_timezone_get();
	
	date_default_timezone_set( 'UTC' );
	
	if ( null === $from_timestamp ) {
		$next_timestamp = strtotime( $time_string );
	} else {
		$next_timestamp = strtotime( $time_string, $from_timestamp );
	}
	
	date_default_timezone_set( $original_timezone );
	
	return $next_timestamp;
}