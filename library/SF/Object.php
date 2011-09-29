<?php
	abstract class SF_Object {
		private $id = null;
				
		public function setId($id) {
			$this->id = (int) $id;
		}
		
		public function getId() {
			return $this->id;
		}

        abstract public function __toString();
	}