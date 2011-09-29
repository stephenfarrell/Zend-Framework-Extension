<?php

/**
 * Main startup class for a ZF application
 * It would be better to extend Zend_Application but then we can't make the framework
 * look after include paths so we just do it ourselves - it's not complimicated anyway
 *
 * @copyright (C)2010 Stephen Farrell All Rights Reserved
 * @package net.stephenfarrell.framework
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @version 1.1
 */

class SF_Application {

    protected $applicationPath = null;
    protected $timezone = null;
    protected $autoloader = null;
    protected $configPath = null;
    protected $layoutsPath;
    protected $controllersPath;

    protected $applicationEnvironment;
    protected $defaultFrameworkConfigPath = 'config/default.xml';
    protected $frameworkPath;

	protected static $log = null;

    const SF_ENV_DEVELOPMENT = 'development';
    const SF_ENV_TEST = 'test';
	const SF_ENV_STAGING = 'staging';
    const SF_ENV_PRODUCTION = 'production';

    protected $allowedEnvironments = array(self::SF_ENV_DEVELOPMENT, self::SF_ENV_TEST, self::SF_ENV_PRODUCTION, self::SF_ENV_STAGING);

	/**
	 * Kick off bootstrapping the app and load up required components
	 * @param string $applicationEnvironment Environment config to load, e.g. dev,test,prod
	 * @param string $applicationPath Full path to the application (to allow for odd shared hosting setups)
	 */
    public function __construct($applicationEnvironment, $applicationPath = null) {

        $this->setApplicationEnvironment($applicationEnvironment);
		
		if(!$applicationPath) {
			$applicationPath = $this->determineApplicationPath();
		}
		$this->setApplicationPath($applicationPath);

        $frameworkPath = $this->determineFrameworkPath();

		$this->setFrameworkPath($frameworkPath);

        $this->setupIncludePaths($frameworkPath, $applicationPath);
		$this->setupAutoloader();

		$config = $this->loadFrameworkConfig($applicationEnvironment);
		//override defaults
		$config = $this->loadApplicationConfig($applicationEnvironment);

		if('true' == $config->bootstrap->session) {
			$this->startSession();
		}

		if('true' == $config->bootstrap->database) {
			$this->databaseConnect();
		}

		//start mvc
		if('true' == $config->bootstrap->mvc) {
			$this->startMvc();
		}

		//default context
		Zend_Registry::set('context', 'desktop');
    }
	
	/**
	 * If no path is provided we'll assume the code is up a level in a dir called 'application'
	 */
	protected function determineApplicationPath() {
		$path = getcwd();
		
		$path .= "/../application";
		
		return $path;
	}
	
	protected function determineFrameworkPath() {
		$frameworkPath = dirname(__FILE__);
		$pathArray = explode(DIRECTORY_SEPARATOR, $frameworkPath);
		array_pop($pathArray);
		$frameworkLibraryPath = implode(DIRECTORY_SEPARATOR, $pathArray);
		array_pop($pathArray);
		$frameworkPath = implode(DIRECTORY_SEPARATOR, $pathArray);
		
		return $frameworkPath;
	}

	protected function setupIncludePaths($frameworkPath, $applicationPath) {
		$frameworkLibraryPath = $frameworkPath . DIRECTORY_SEPARATOR . 'library';
		$applicationLibraryPath = $applicationPath . DIRECTORY_SEPARATOR . 'library';

		set_include_path($frameworkLibraryPath . PATH_SEPARATOR . $applicationLibraryPath . PATH_SEPARATOR . get_include_path());
    }

    protected function loadFrameworkConfig($applicationEnvironment) {
		$config = new Zend_Config_Xml($this->getFrameworkPath() . DIRECTORY_SEPARATOR . $this->getDefaultFrameworkConfigPath(), $applicationEnvironment, true);
		Zend_Registry::set('config', $config);

		return $config;
    }

    protected function loadApplicationConfig($applicationEnvironment) {
		$frameworkConfig = Zend_Registry::get('config');

		$path = $this->getApplicationPath() .  DIRECTORY_SEPARATOR . $frameworkConfig->environment->config->folder . DIRECTORY_SEPARATOR . $frameworkConfig->environment->config->files->application;
		//load up the app config and override the framework defaults
		$appConfig = new Zend_Config_Xml($path, $this->getApplicationEnvironment());

		$frameworkConfig->merge($appConfig);

		Zend_Registry::set('config', $frameworkConfig);

		return $frameworkConfig;
    }

    public function startMvc() {
		$config = Zend_Registry::get('config');
		$layoutPath = $this->getApplicationPath() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $config->environment->layouts_path);
	//	echo $layoutPath;exit;
		Zend_Layout::startMvc($layoutPath);
    }

    public function setupAutoloader() {
    	require_once "Zend/Loader/Autoloader.php";

        //Zend_Loader::registerAutoload();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();
        //tell the autoloader to look at the SF framework stuff as well
        $this->autoloader->registerNamespace('SF_');
    }

    public function registerAutoloadNamespace($namespace) {
        $this->autoloader->registerNamespace($namespace);
    }

    public function setApplicationPath($applicationPath) {
//		Zend_Registry::set('application_path', $applicationPath);
        $this->applicationPath = $applicationPath;

        return $this;
    }

    public function databaseConnect() {
		$config = Zend_Registry::get('config');
		if(isset($config->database)) {
			$options = $config->database->params;
			$adapter = $config->database->adapter;
			$db = Zend_Db::factory($adapter, $options);

			Zend_Db_Table_Abstract::setDefaultAdapter($db);
			Zend_Registry::set('db', $db);
		}
    }

    public function startSession() {
	Zend_Session::start();
    }

    private function getRouter($environment) {
		$router = new SF_Controller_Router_Rewrite();

		$router->addStaticRoutes($environment);

		return $router;
    }

	public static function getLog() {
		if(null === self::$log) {
			self::$log = new SF_Log();
		}
		return self::$log;
	}

	public static function log($message, $level = null, $source = null) {
		$log = self::getLog();

		//bit expensive but we'll see how it goes
		$debug = debug_backtrace();
//		$backtrace = base64_encode(print_r($debug,1));
		
		if(null === $source) {
			$source = $debug[1]['class'] . '::' . $debug[1]['function'];


		}

		$log->setEventItem('source', $source);
		$log->setEventItem('backtrace', $debug);

		return $log->log($message, $level);
	}

    public function dispatch() {
		

		try {
			
			$frontController = $this->initFrontController();
			
			$frontController->dispatch();
		} catch (Exception $exception) {
			echo '<html><body><center>'  . 'An exception occured while bootstrapping the application.';
			if ($this->getApplicationEnvironment() != self::SF_ENV_PRODUCTION ) {
			echo '<br /><br />' . $exception->getMessage() . '<br />'  . '<div align="left">Stack Trace:' . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
			}
			echo '</center></body></html>';
			exit(1);
		}
    }
	
	protected function initFrontController() {
		$config = Zend_Registry::get('config');
		$controllerPath = $this->getApplicationPath() . DIRECTORY_SEPARATOR . $config->environment->controllers_path;
		
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->setControllerDirectory($controllerPath);

		$frontController->setParam('env', $this->getApplicationEnvironment());

		$frontController->setRouter($this->getRouter($this->getApplicationEnvironment()));

		$this->initPlugins($frontController);
		
		return $frontController;
	}
	
	protected function initPlugins($frontController) {
		$frontController->registerPlugin(new SF_Plugin_Mobile());
	}

	public function getApplicationPath() {
        return $this->applicationPath;
    }

    public function setTimezone($timezone) {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function setConfigPath($configPath) {
        $this->configPath = $configPath;

        return $this;
    }

    public function getConfigPath() {
        return $this->configPath;
    }

    public function getAllowedEnvironments() {
		return $this->allowedEnvironments;
    }

    public function setApplicationEnvironment($applicationEnvironment) {

		if(!in_array($applicationEnvironment, $this->getAllowedEnvironments())) {
			throw new Exception('Invalid Environment ' . $applicationEnvironment . ' selected');
		}

        $this->applicationEnvironment = $applicationEnvironment;

        return $this;
    }

    public function getApplicationEnvironment() {
        return $this->applicationEnvironment;
    }

    public function getDefaultFrameworkConfigPath() {
		return $this->defaultFrameworkConfigPath;
    }

    protected function setFrameworkPath($frameworkPath) {
		$this->frameworkPath = $frameworkPath;

		return $this;
    }

    public function getFrameworkPath() {
		return $this->frameworkPath;
    }
}