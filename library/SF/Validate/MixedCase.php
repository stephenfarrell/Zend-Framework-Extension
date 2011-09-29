<?php
	class SF_Validate_MixedCase extends Zend_Validate_Abstract {

		const NO_UPPERCASE = 'no_uppercase';

		protected $_messageTemplates = array(
	        self::NO_UPPERCASE => "Value must be mixed case",
	    );

		public function isValid($value) {
			$result = true;

			$this->_setValue($value);

			if(0 == strlen(trim($value))) {
				$result = false;
			} else {

				//contains at least one lower case character
				if(!preg_match('$[a-z]$', $value)) {
					$result = false;
				}

				//contains at least one upper case character
				if(!preg_match('$[A-Z]$', $value)) {
					$result = false;
				}

				if(!$result) {
					$this->_error();
				}
			}

			return $result;
		}
	}