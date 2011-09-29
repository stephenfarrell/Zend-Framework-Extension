<?php

/**
 * Class for managing UI themes in an SF framework app.
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @version 1.0
 * @license Closed - Not for redistribution
 * @copyright (C)2010 Stephen Farrell - All Rights Reserved
 */
class SF_Theme {

	const THEME_CONFIG_FILE = 'theme.xml';

	protected static $_theme = null;

	/**
	 * Static function to return a list of all themes
	 * within the themes folder specified in the config
	 *
	 * @author Stephen Farrell <stephen@stephenfarrell.net>
	 * @version 1.0
	 * @since 1.0
	 *
	 * @returns array Array of theme names
	 */
	public static function themes() {

		$themes = array();
//		$config = Zend_Registry::get('config');
		$themes_dir = self::themes_dir();
		
		$handle = opendir($themes_dir);
		while (false !== ($file = readdir($handle))) {
			if(!in_array($file, array('.', '..'))) {

				$theme_config = new Zend_Config_Xml($themes_dir . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . self::THEME_CONFIG_FILE);

				$themes[] = array('name' => $file, 'title' => $theme_config->title);
			}
		}

		return $themes;
	}

	public static function themes_dir() {
		$config = Zend_Registry::get('config');
		return $config->environment->application_path . DIRECTORY_SEPARATOR . $config->themes->themes_dir;
	}

	public static function set_theme($theme) {
		self::$_theme = $theme;
		//override the standard layouts folder and switch to the selected themes layouts
		Zend_Layout::getMvcInstance()->setLayoutPath(self::layout_dir($theme));
	}

	public static function get_theme() {
		return self::$_theme;
	}

	public static function layout_dir($theme = null) {
		if(!$theme) {
			$theme = self::get_theme();
		}

		$config = Zend_Registry::get('config');
		$layout_dir = self::themes_dir() . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $config->themes->themes_layouts_dir;

		return $layout_dir;
	}
}