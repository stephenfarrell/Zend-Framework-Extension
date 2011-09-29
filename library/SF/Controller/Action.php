<?php
/**
 * Class for application level controllers to extend in the SF framework.
 *
 * Handles dispatch and adds _before and _after hooks to any action
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @version 1.0
 *
 * @uses Zend_Controller_Action
 */
class SF_Controller_Action extends Zend_Controller_Action {

	public $data = NULL;

	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct($request, $response, $invokeArgs);
		// turn off auto-rendering of views
		//$this->_helper->viewRenderer->setNoRender();

		$this->setupContexts();
	}

	protected function setupContexts() {
		$supportedContexts =
            array(
                'mobile' => array(
                    'suffix'  => 'mobile',
                    'headers' => array(
                        'Content-type' => 'text/html; charset=utf-8'
					)
				)
            );


		$functions = array();
		foreach (get_class_methods($this) as $method) {
			if (strstr($method,"Action") != false) {
				array_push($functions,substr($method,0,strpos($method,"Action")));
			}
		}

		// Init the action helper
    	$contextSwitch = $this->_helper->contextSwitch();
		$contextSwitch->setContexts($supportedContexts);
		
		foreach($functions as &$function) {
			$function = strtolower(preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '-$0', $function));
			
			//add the mobile context to every action by default
			$contextSwitch->addActionContext($function, 'mobile');
		}
		// enable layout but set different path to layout file
    	$contextSwitch->setAutoDisableLayout(false);
		
		//determine the context
		if('mobile' == Zend_Registry::get('context')) {
			$this->setMobileContext($contextSwitch);
		}

	}

	protected function setMobileContext($helper) {
		$layoutPath = $this->getHelper('layout')->getLayoutPath();

		$this->getHelper('layout')->setLayoutPath($layoutPath . DIRECTORY_SEPARATOR . 'mobile');

		$helper->initContext('mobile');
	}
	
	public function isMobileContext() {
		return 'mobile' == Zend_Registry::get('context');
	}

	public function dispatch($action) {
		//check for post data so we don't have to be accessing the post array directly in our controller
		if ($this->getRequest()->isPost()) {
				$this->data = $_POST;
		}

		return parent::dispatch($action);
	}

		/**
	 * Dispatch the requested action
	 *
	 * @param string $pAction Method name of action
	 * @return void
	 * @deprecated Renamed from dispatch as functionality has been moved to plugins
	 */
	public function _dispatch($pAction)
	{
		// Notify helpers of action preDispatch state
		$this->_helper->notifyPreDispatch();

		//$this->preDispatch();

		//little bit dirty, but I think its the handiest way of making the proper base/root url of the app available in the views (including the layout)
		$vFrontController = Zend_Controller_Front::getInstance();

		$vBaseURL = rtrim($vFrontController->getBaseUrl(), '/');

		/*
		if(str_replace('index.php','', $vBaseURL) == '/') {
			//$vBaseURL = ltrim($vBaseURL,'/');
		}
		*/

		/*
		if('' == $vBaseURL) {
			$vBaseURL = '/';
		}
		*/

		$this->view->base_url = $vBaseURL;
		$this->view->base_resource_url = str_replace('index.php','', $vBaseURL); //if url rewriting isnt being used it will mess up our base url for loading images, css etc

		if('/' == $this->view->base_resource_url) {
			$this->view->base_resource_url = '';
		}

		//force Zend_Form to output proper code!
		$this->view->doctype('XHTML1_STRICT');

		unset($vBaseURL);

		//check for post data so we don't have to be accessing the post array directly in our controller
		if ($this->getRequest()->isPost()) {
				$this->data = $_POST;
		}


		$this->preDispatchHook($pAction);
		if ($this->getRequest()->isDispatched()) {
			// preDispatch() didn't change the action, so we can continue
			if ($this->getInvokeArg('useCaseSensitiveActions') || in_array($pAction, get_class_methods($this))) {
				if ($this->getInvokeArg('useCaseSensitiveActions')) {
					trigger_error('Using case sensitive actions without word separators is deprecated; please do not rely on this "feature"');
				}
				$this->$pAction();
			} else {
				$this->__call($pAction, array());
			}
			//$this->postDispatch();
			$this->postDispatchHook($pAction);
		}

		// whats actually important here is that this action controller is
		// shutting down, regardless of dispatching; notify the helpers of this
		// state
		$this->_helper->notifyPostDispatch();
	}

	/**
	 * Call post action function and enable chaining
	 * @param string $pAction Method name of action
	 *
	 * @deprecated Not used anymore as plugins provide these hooks
	 */
	public function preDispatchHook($pAction) {
		$vRequest = $this->getRequest();

		//call _after{ActionName} function
		$vHook = '_before' . ucfirst($pAction);

		if(method_exists($this, $vHook)) {
			$this->$vHook();
		}
	}

	/**
	 * Call post action function and enable chaining
	 * @param string $pAction Method name of action
	 *
	 * @deprecated Not used anymore as plugins provide these hooks
	 *
	 */
	public function postDispatchHook($pAction) {
		$vRequest = $this->getRequest();

		//call _after{ActionName} function
		$vPostHook = '_after' . ucfirst($pAction);

		if(method_exists($this, $vPostHook)) {
			$this->$vPostHook();
		}
	}

	/**
	 * Alias for the actionStack helper
	 *
	 * @param unknown_type $pAction
	 * @param unknown_type $pController
	 * @param unknown_type $pSomething
	 * @param array $pParams
	 */
	public function chain($pAction, $pController = null, $pSomething = 'default', array $pParams = array()) {
		$pParams['chained'] = true;
		$this->_helper->actionStack($pAction, $pController, $pSomething, $pParams);
	}

	/**
	 * Handy alias for getting URL params
	 *
	 * @param string $pName Name of parameter value to retrieve
	 * @return parameter value
	 */
	public function getParam($pName) {
		return $this->getRequest()->getParam($pName);
	}

	public function loadValueObject($pEntityName, $pAutoloadDAO = true) {

		$vDAOName = $pEntityName . 'DAO';
		$config = Zend_Registry::get('config');
		$vVOPath = $config->environment->application_path . DIRECTORY_SEPARATOR . $config->environment->objects_path . DIRECTORY_SEPARATOR . $pEntityName . '.php';;


		if(file_exists($vVOPath)) {
			include_once($vVOPath);
		} else {
			trigger_error('Value object ' . $pEntityName . ' not found in ' . $vVOPath);
		}

		if($pAutoloadDAO) {
			$this->loadDAO($vDAOName);
		}
	}

	public function loadDAO($pDAOName) {
		$config = Zend_Registry::get('config');
		$vDAOPath = $config->environment->application_path . DIRECTORY_SEPARATOR . $config->environment->daos_path . DIRECTORY_SEPARATOR . $pDAOName . '.php';
		if(file_exists($vDAOPath)) {
			include_once($vDAOPath);

			//$this->{$pDAOName} = new $pDAOName();
			eval('$this->' . $pDAOName . " = {$pDAOName}::getInstance();");

		} else {
			trigger_error('DAO ' . $pDAOName . ' not found in ' . $vDAOPath);
		}
	}

	public function loadModel($pModelName) {
		$config = Zend_Registry::get('config');
		$vModelPath = $config->environment->application_path . DIRECTORY_SEPARATOR . $config->environment->models_path . DIRECTORY_SEPARATOR .$pModelName . '.php';

		if(file_exists($vModelPath)) {
			include_once($vModelPath);

			$this->{$pModelName} = new $pModelName();

		} else {
			trigger_error('Model ' . $pModelName . ' not found in ' . $vModelPath);
		}
	}

}