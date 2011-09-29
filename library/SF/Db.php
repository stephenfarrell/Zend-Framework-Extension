<?php

	//include 'Zend' . DIRECTORY_SEPARATOR . 'Db.php';

	class SF_Db extends Zend_Db {
		
		protected static $mInstance = null;
		
		public static function getInstance() {
			
	        if (null === self::$mInstance) {
	        	// Automatically load class Zend_Db_Adapter_Pdo_Mysql and create an instance of it.
	        	
	        	$config = Zend_Registry::get('config');
				try {
		        	self::$mInstance = Zend_Db::factory($config->database->params->adapter, array(
						'host'     => $config->database->params->host,
						'username' => $config->database->params->username,
						'password' => $config->database->params->password,
						'dbname'   => $config->database->params->dbname
					));
				} catch (Zend_Db_Adapter_Exception $e) {
				    // perhaps a failed login credential, or perhaps the RDBMS is not running
				    trigger_error('Unable to connect to DB');
				} catch (Zend_Exception $e) {
				    // perhaps factory() failed to load the specified Adapter class
				}				
	        }
	
	        return self::$mInstance;
		}
		
	}