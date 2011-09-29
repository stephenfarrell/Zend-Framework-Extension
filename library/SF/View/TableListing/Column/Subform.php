<?php

	class SF_View_TableListing_Column_Subform extends SF_View_TableListing_Column {

		protected $subform;

		public function setSubform($subform) {
			$this->subform = $subform;

			return $this;
		}

		public function getSubform() {
			return $this->subform;
		}

		public function toString() {
			$rowNum = $this->getRowNum();

			$elements = $this->getSubform()->getElements();
//			echo '<pre>' .print_r($elements,1);exit;
			$element = $elements[$rowNum];

			//kludgetastic
			foreach($element->getDecorators() as $decorator) {
				$name = get_class($decorator);
				if($name != 'Zend_Form_Decorator_HtmlTag' && $name != 'Zend_Form_Decorator_ViewHelper') {
					$element->removeDecorator($name);
				}
			}

			//even kludgier - when rendering the subform like this we lose the fields association with the subform
			$element->setBelongsTo($this->getSubform()->getName());

			return "<td>" . $element . "</td>";
		}
	}