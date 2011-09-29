<?php
	class SF_View_TableListing {
		
		private $mResults = array();
		
		private $mColumns = array();
		
		private $mTableClass = 'listing';
		
		private $mFooter = '';
		
		private $mOddRowClass = 'rowodd';
		private $mEvenRowClass = 'roweven';
		
		public function setResults($pResults) {
			$this->mResults = $pResults;
		}
		
		public function setColumns(array $pColumns) {
			$this->mColumns = $pColumns;
		}
		
		public function setTableFooter($pFooter) {
			$this->mFooter = $pFooter;
		}		
		
		public function getTableFooter() {
			return $this->mFooter;
		}
		
		public function generateTable() {
			$vHtml = "\n<table class=\"" . $this->getClass() . "\">\n";
			$vTHead = '';
			
			if(0 == count($this->mColumns)) {
				if(count($this->mResults) > 0) {
					$vTHead .= "<thead><tr>\n";
					$vFirstRow = $this->mResults[0];
					foreach ($vFirstRow as $vHeader => $vValue) {
						$vTHead .= '<th>' . $vHeader . '</th>';
					}
					$vTHead .= "\n</tr></thead>\n";
				}
			}
			
			$vTbody = "<tbody>\n";
			
			$vRowNum = 0;
			$vPreviousRow = null;
			foreach ($this->mResults as $vRow) {
				$vRowClass = ($vRowNum%2 == 0) ? $this->getEvenRowClass() : $this->getOddRowClass();
				
				$vIsObject = is_subclass_of($vRow, 'SF_Object');
			
				$vTbody .= "<tr class=\"{$vRowClass}\">\n";
				
				if(count($this->mColumns)) {
					$vColNum = 0;
					foreach ($this->mColumns as $vTableColumn) {
						
						$vColValue = NULL;
						
						//row can be an array of columns from a select or it could be an object
						if($vIsObject) {
							//row is a object so check the table column to see what accessor function we should use
							if($vTableColumn->getAccessorName()) {
								$vColValue = $vRow->{$vTableColumn->getAccessorName()}();
							}
						} else {						
							//try and get the value from the select alias
							if(!empty($vRow[$vTableColumn->getAlias()])) {
								$vColValue = $vRow[$vTableColumn->getAlias()];
							} 
						}						
						
						$vTableColumn->setValue($vColValue);
						$vTableColumn->setRow($vRow);
						$vTableColumn->setRowNum($vRowNum);
						$vTableColumn->setPreviousRow($vPreviousRow);
						
						if($vTableColumn->getVisible()) {
							
							if(1 == $vRowNum) {
								$vTHead .= '<th class="' . $vTableColumn->getClass() . '">' . $vTableColumn->getTitle() . '</th>';
							}
							
	 						//$vTbody .= '<td>';
							$vTbody .= $vTableColumn->toString() . "\n";
							//$vTbody .= '</td>';
						}
						
						$vColNum++;
					}
				} else {
					// we dont have any columns set so just display the raw data
					foreach ($vRow as $vCol) {
						$vTbody .= '<td>';
						$vTbody .= $vCol;
						$vTbody .= '</td>';
					}
				}
				
				$vTbody .= '</tr>';
				$vRowNum++;
				$vPreviousRow = $vRow;
			}
			
			if(count($this->mColumns)) {
				$vTHead = "<thead>\n<tr>\n$vTHead\n</tr>\n</thead>\n";
			}
			
			$vTbody .= "</tbody>\n";
			
			$vTFooter = '<tfoot>' . $this->getTableFooter() . '</tfoot>';
			
			
			
			$vHtml .= $vTHead . $vTbody . $vTFooter . '</table>';
			
			return $vHtml;
		}
		
		public function setClass($pClass) {
			$this->mTableClass = $pClass;
		}
		
		public function getClass() {
			return $this->mTableClass;
		}
		
		public function setOddRowClass($pOddRowClass) {
			$this->mOddRowClass;
		}
		
		public function getOddRowClass() {
			return $this->mOddRowClass;
		}
		
		public function setEvenRowClass($pEvenRowClass) {
			$this->mEvenRowClass =$pEvenRowClass;
		}
		
		public function getEvenRowClass() {
			return $this->mEvenRowClass;
		}
	}
?>