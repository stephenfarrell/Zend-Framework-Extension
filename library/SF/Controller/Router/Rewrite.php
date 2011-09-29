<?php
/**
 * Class for controlling routing in the SF framework.
 * Sets up a list of static page url routes based on the static_routes.xml
 * config file.
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @version 1.0
 *
 * @uses Zend_Controller_Router_Rewrite
 */
class SF_Controller_Router_Rewrite extends Zend_Controller_Router_Rewrite {
	public function addStaticRoutes($environment) {
		$path = Zend_Registry::get('config')->environment->application_path;
		//load routes config file
		$routes = new Zend_Config_Xml($path.'/config/static_routes.xml', $environment);

		foreach ($routes as $route) {
			if(is_object($route)) {

				$url = $route->url;
				$controller = $route->controller;
				$action = $route->action;
				$routeArray = array(
					'controller' => $route->controller
				  , 'action' => $route->action
				);
				if(count($route->params)) {
					foreach ($route->params as $param) {
						$routeArray[$param->name] = $param->value;
					}
				}

				$this->addRoute($url, new Zend_Controller_Router_Route_Static($url, $routeArray));
			}
		}
	}
}