<?php

	class SF_Errors {
		static private $mErrors = array();
		/*
		public static function getInstance() {
			
	        if (null === self::$mInstance) {
	        	self::$mInstance = new self();
	        }
	
	        return self::$mInstance;
		}
		*/
		static public function add($pError) {
			self::$mErrors[] = $pError;
		}
		
		static public function numErrors() {
			return count(self::$mErrors);
		}
		
		static public function clear() {
			self::$mErrors = array();
		}
		
		static public function noErrors() {
			return (0 == count(self::$mErrors));
		}
		
		static public function output() {
			$vErrorList = null;
			
			if(!self::noErrors()) {
				$vErrorList = '<ul class="sf-errors">';
				foreach (self::$mErrors as $vError) {
					$vErrorList .= "<li>$vError</li>";
				}
				$vErrorList .= '</ul>';
			}
			return $vErrorList;
		}
	}