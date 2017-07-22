<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Allows log files to be written to for debugging purposes
 *
 * @class          BMPS_Logger
 * @version        1.0.0
 * @package        bmps/Classes
 * @category       Class
 * @author         Codesniper
 */
class BMPS_Logger
{
    /**
     * File _handles
     */

    private $_handles;

    /**
     * Logger Constructer
     */

    public function __construct()
    {
        $this->_handles = array();
    }

    public function __destruct()
    {
        foreach ( $this->_handles as $handle ) {
            if( is_resource( $handle ) ) {
                fclose( $handle );
            }
        }
    }

    /**
     * Open the log file to write logs
     *
     * @param string $handle
     * @param string $mode
     * @return bool success
     */

    protected function open( $handle, $mode = 'a' ) {
        if( isset( $this->_handles[$handle] ) ) {
            return true;
        }
        if( $this->_handles[ $handle ] = @fopen( bmps_get_log_file_path( $handle ), $mode ) ) {
            return true;
        }

        //default
        return false;
    }

    /**
     * Close handle
     * @param string $handle
     * @return bool success
     */

    protected function close( $handle ) {
        $result = false;
        if( is_resource( $this->_handles[ $handle ] ) ) {
            $result = fclose( $this->_handles[ $handle ] );
            unset( $this->_handles[ $handle ] );
        }

        return $result;
    }

    /**
     *
     * Make a log entry to choosen file
     * @param string $handle
     * @param string $message
     * @return bool
     */

    public function add( $handle, $message ) {
        $result = false;
        if( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
            $time = date_i18n( 'm-d-Y @ H:i:s -' ); //get time
            $result = fwrite( $this->_handles[ $handle ], $time . " ". $message .'\n' );
        }
        do_action( 'bmps_add_log', $handle, $message  );
        return false !== $result;
    }

    public function cleaar( $handle ) {
        $result = false;

        //Close the file if already open
        $this->close( $handle );

        /**
         * Open the file for writing
         * Place the pointer at beginning of file
         * Truncate the file to zero length
         */
        if( $this->open( $handle, 'w' ) && is_resource( $this->_handles[ $handle ] ) ) {
            $result = true;
        }

        do_action( 'bmps_log_clear', $handle );
        return $result;
    }
}