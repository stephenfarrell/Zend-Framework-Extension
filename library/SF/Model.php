<?php
	abstract class SF_Model {
		
		const FEEDBACK_CREATED = 1;
		const FEEDBACK_UPDATED = 2;
		const FEEDBACK_DELETED = 3;
		
		const UPDATE_MODE = 'update';
		const ADD_MODE = 'add';
		const CHANGE_PASSWORD_MODE = 'change_password';
		
		private $entityName = 'Record';
		
		public function validate(V1_Object $pObject = null, $mode = null) {
			return false;
		}
		
		public function populateFormFromObject(Zend_Form $pForm, $pObject) {
			foreach ($pForm->getValues() as $vField => $vValue) {
				if(method_exists($pObject, 'get' . $vField)) {
					$vGetter = 'get' . $vField;
					
					$pForm->{$vField}->setValue($pObject->$vGetter());
				}
			}
			
			return $pForm;
		}
		
		public function populateObjectFromForm(Zend_Form $pForm, $pObject) {
			foreach ($pForm->getValues() as $vField => $vValue) {
				if(method_exists($pObject, 'set' . $vField)) {
					$vSetter = 'set' . $vField;
					$pObject->$vSetter($vValue);
				}
			}
			return $pObject;
		}

		public function populateObjectsFromForm(Zend_Form $form, $objects) {
			foreach ($form->getValues() as $field => $value) {
				//echo "<pre>{$field} = {$value}</pre>";

				if(is_array($value)) {
					foreach($value as $idx => $val) {

						if(isset($objects[$idx])) {
							$object = $objects[$idx];
							if(method_exists($object, 'set' . $field)) {
								$setter = 'set' . $field;
								$object->$setter($val);
							}
						}
					}
				}
			}

			return $objects;
		}
		
		public function getFeedbackMessage($code) {
			switch ($code) {
				case self::FEEDBACK_CREATED :
					$message =  $this->entityName . ' added successfully';
					break;
				case self::FEEDBACK_UPDATED :
					$message = $this->entityName . ' updated successfully';
					break;
			}
			
			return $message;
		}
		
	}