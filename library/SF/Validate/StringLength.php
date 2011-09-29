<?php
	class SF_Validate_StringLength extends Zend_Validate_StringLength {
		
		protected $_messageTemplates = array(
	        self::TOO_SHORT => "Value must be between %min% and %max% characters long",
	        self::TOO_LONG  => "Value must be between %min% and %max% characters long"
	    );		
	}