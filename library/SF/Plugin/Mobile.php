<?php
/**
 * Plugin to detect and handle requests coming from a mobile device
 */
class SF_Plugin_Mobile extends Zend_Controller_Plugin_Abstract {

	protected static $userAgent;

	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		
		$device = self::userAgent()->getDevice();		
		$allFeatures = $device->getAllFeatures();
		
		$deviceId = null;
		$tablet = false;
		
		if(isset($allFeatures['device'])) {
			$deviceId = $allFeatures['device'];
		}
		
		if(isset($allFeatures['is_tablet'])) {
			$tablet = $allFeatures['is_tablet'];
		}
		
		//don't treat tablets as mobile		
		$isTablet = ($tablet == 1);
		
		if('mobile' == $device->getType() && !self::isIpad() && !$isTablet) {
			//set the context to mobile
			Zend_Registry::set('context', 'mobile');
		} else {
			Zend_Registry::set('context', 'desktop');
		}
	}
	
	public static function userAgent() {
		if(!self::$userAgent) {
			$config = Zend_Registry::get('config')->toArray();
			self::$userAgent = new Zend_Http_UserAgent($config['resources']['useragent']);
		}
		
		return self::$userAgent;
	}
	
	public static function getAllFeatures() {
		return self::userAgent()->getDevice()->getAllFeatures();
	}
	
	public static function getDeviceIdentifier() {
		$result = null;
		
		$allFeatures = self::getAllFeatures();
	
		if(isset($allFeatures['device'])) {
			$result = $allFeatures['device'];
		} else {
			//kludge for iphone simulator
			
			if(isset($allFeatures['compatibility_flag']) && 'iPhone Simulator' == $allFeatures['compatibility_flag']) {
				$result = 'iphone';
			}			
		}
		unset($allFeatures);
		
		return $result;
	}
	
	public static function isIphone() {		
		return (strtolower(substr(self::getDeviceIdentifier(),0,6)) == 'iphone');		
	}
	
	public static function isIpod() {
		return (strtolower(substr(self::getDeviceIdentifier(),0,4)) == 'ipod');
	}
	
	public static function isIpad() {
		return (strtolower(self::getDeviceIdentifier()) == 'ipad');
	}
	
	public static function isAndroid() {
		return null; //not implemented
	}
}