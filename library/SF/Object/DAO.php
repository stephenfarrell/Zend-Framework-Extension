<?php
	
	abstract class SF_Object_DAO extends Zend_Db_Table implements SF_Singleton {
		
		protected $db = null;
		
		public function getAll() {}
		
		public function get($id, $lookups = false) {
			$result = $this->find($id);
			
			$result = $result->getRow(0);

            $object = $this->populateObject($result);

            if($lookups) {
                //get parent rows (lookup values)
                foreach($this->_referenceMap as $objectName => $reference) {
                    $parentDAO = $reference['refTableClass'];
                    $parent = $result->findParentRow($parentDAO);
                    //convert the rowset to an array
                    $parent = $parent->toArray();


                    $dao = new $parentDAO();

                    $populateMethod = 'populate' . $objectName;
                    $setterMethod  = 'set' . $objectName;

                    if(!method_exists($dao, $populateMethod)) {
                        $populateMethod = 'populateObject';
                    }

                    $parentObj = $dao->$populateMethod($parent);

                    $object->$setterMethod($parentObj);
                    unset($dao);
                }
            }

			return $object;
		}
		
		public function getValueFromResult($result, $key) {
			if(is_array($result)) {
				return $this->getValueFromArray($result, $key);
			} else {
				return $this->getValueFromObject($result, $key);
			}
		}
		
		public function getValueFromObject($object, $key) {
			$value = null;
			if(isset($object->{$key})) {
				$value = $object->{$key};
			}
			
			return $value;
		}
		
		public function getValueFromArray($pArray, $pKey) {
			$vValue = null;
			if(isset($pArray[$pKey])) {
				$vValue = $pArray[$pKey];
			}
			
			return $vValue;
		}

        public function db() {
            return $this->getAdapter();
        }
		
		abstract public function populateObject($row);

	} 