<?php
/**
 * Class for writing log entries to an Oracle database using a stored procedure
 *
 * @author Stephen Farrell <stephen@stpehenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @package net.stephenfarrell.framework
 * @version 1.0
 *
 * @uses Zend_Log_Writer_Db
 */
class SF_Log_Writer_Db_Sproc extends Zend_Log_Writer_Db {

	public function __construct() {
		$this->_db = Zend_Registry::get('db');
	}

	static public function  factory($config) {
		return new self();
	}

	/**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event) {
        if ($this->_db === null) {
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Database adapter is null');
        }

		$sproc = Zend_Registry::get('config')->logging->storage->sproc;

		//dont write if logging is disabled
		if('true' == Zend_Registry::get('config')->logging->enabled) {
			$sql = "BEGIN $sproc(:iv_source, :iv_message, :in_level, :iv_ora_code, :iv_backtrace, :on_error); END;";

			$err_id = '-99999999';

			$bindVars = array(
				'iv_source'		=> $event['source']
			  , 'iv_message'	=> $event['message']
			  , 'in_level'		=> $event['priority']
			  , 'iv_ora_code'	=> null
			  , 'iv_backtrace'	=> null//$event['backtrace']
			  , 'on_error'		=> &$err_id
			);

			$results = $this->_db->query($sql, $bindVars);
		}
    }
}