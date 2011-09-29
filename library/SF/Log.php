<?php
/**
 * Class for instantiating logging in the SF framework
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @version 1.0
 *
 * @uses Zend_Log
 */
class SF_Log extends Zend_Log {

	public function  __construct(Zend_Log_Writer_Abstract $writer = null) {

		if(null === $writer) {
			$config = Zend_Registry::get('config');

			$writerClass = 'SF_Log_Writer_' . $config->logging->storage->type;

			$writer = new $writerClass();
		}

		return parent::__construct($writer);
	}
}