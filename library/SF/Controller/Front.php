<?php
/**
 * Class for creating the Front Controller for an SF framework application
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @version 1.0
 *
 * @uses Zend_Controller_Front
 */
class SF_Controller_Front extends Zend_Controller_Front {

	protected $application;

	/**
	 * Singleton instance
	 *
	 * @return SF_Controller_Front
	 */
	public static function getInstance()
	{
	if (null === self::$_instance) {
		self::$_instance = new SF_Controller_Front();
	}

	return self::$_instance;
	}

	public function __construct() {


//	    	parent::__construct();
		//$this->returnResponse(true);
	die(get_class($this));
	return $this;
	}

	public function getApplication() {
	return $this->application;
	}

	public function setApplication(SF_Application $application) {
	$this->application = $application;

	return $this;
	}

}