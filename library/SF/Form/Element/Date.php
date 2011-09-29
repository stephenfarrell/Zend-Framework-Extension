<?php
/**
 * Class for rendering a date field within a Zend_Form instance
 *
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 * @copyright (C) 2010 Stephen Farrell - All Rights Reserved
 * @license Closed - Not for redistribution
 * @version 1.0
 *
 * @uses Zend_Form_Element_Xhtml
 */
class SF_Form_Element_Date extends Zend_Form_Element_Xhtml
{
	public $helper = 'formDate';

	public function init ()
	{
		$this->addValidator(new SF_Validate_Date());
	}

	public function isValid ($value, $context = null)
	{
		// ignoring value -- it'll be empty

		$name = $this->getName();

		$value = $context[$name . '_year'] . '-' .
					$context[$name . '_month'] . '-' .
					$context[$name . '_day'];

		if('--' == $value) {
			$value = null;
		}
		
		$this->_value = $value;

		return parent::isValid($value, $context);
	}

}