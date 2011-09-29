<?php
	class SF_View_TableListing_Column_MultipleLinks extends SF_View_TableListing_Column {
		
		protected $mLinkPath = '';
		protected $mConditional = NULL;
		protected $mDisplayConditional = false;
		protected $mLinkText = NULL;
		protected $mLinkClass = '';
		
		protected $mSubquery = null;
		
		public function setLinkPath($pPath) {
			$this->mLinkPath = $pPath;
		}
		
		public function getLinkPath() {
			return $this->mLinkPath;
		}
		
		public function setConditional($pConditional, $pDisplay = false) {
			$this->mConditional = $pConditional;
			$this->mDisplayConditional = $pDisplay;
		}
		
		public function getConditional() {
			return $this->mConditional;
		}
		
		public function setLinkText($pLinkText) {
			$this->mLinkText = $pLinkText;
		}
		
		public function getLinkText() {
			return $this->mLinkText;
		}
		
		public function setLinkClass($pLinkClass) {
			$this->mLinkClass = $pLinkClass;
		}
		
		public function getLinkClass() {
			return $this->replaceVars($this->mLinkClass, ' ');
		}
		
		public function setSubquery($pSubquery) {
			$this->mSubquery = $pSubquery;
		}
		
		public function getSubquery() {
			return $this->mSubquery;
		}
		
		public function toString() {

			$vValue = $this->getValue();	
			
			$vSubquery = $this->replaceVars($this->getSubquery());			
			
			$db = V1_Db::getInstance();
			
//			echo "<pre>$vSubquery</pre>";exit;
			
			$vSubResults = $db->fetchAll($vSubquery);
			
			$vHtml = '<td' . ('' != $this->getClass() ? ' class="' . $this->getClass() . '"' : '') . '>';
			$vLinks = array();
			
			
			foreach($vSubResults as $vResult) {							
				$vTooltip = '';
				
				if(NULL != $this->getImagePath()) {
					$vValue = '<img src="' . $this->getImagePath() . '" alt="' . $vValue . '" class="no-border" />';
				}
				
				if(NULL != $this->getTooltip()) {
					$vTooltip = 'title="' . htmlentities($this->replaceVars($this->getTooltip(), ' ', $vResult)) . '"';
				}
				
				if(NULL !== $this->getLinkText()) {
					$vValue = $this->replaceVars($this->getLinkText(), ' ', $vResult);
				}			
				
				$vLinkClass = $this->getLinkClass();
				
				
				
				$vLinks[] = '<a href="' . $this->replaceVars($this->mLinkPath, '/', $vResult) . '" ' . $vTooltip . ' class="' . $vLinkClass . '">' . $vValue . '</a>';
				/*
				if($this->conditionalTest()) {
					$vHtml .= '<a href="' . $this->replaceLinkVars($this->mLinkPath) . '" ' . $vTooltip . ' class="' . $vLinkClass . '">' . $vValue . '</a>';
				} else {
					if($this->mDisplayConditional) {
						$vHtml .= $vValue;
					} else {
						$vHtml .= '&nbsp;';
					}
				}
				*/
			}
			$vHtml .= implode(', ', $vLinks) . '</td>';
			return $vHtml;
		}
		
		private function conditionalTest() {
			$vResult = true;
			$vConditon = $this->getConditional();
			if(null !== $vConditon) {
				$vConditionVal = $this->replaceVars($vConditon,'/');
				if($vConditionVal) {
					$vResult = true;
				} else {
					$vResult = false;
				}
			}
			
			return $vResult;
		}
		
		public function replaceLinkVars($pLink) {
			/*
			$vParts = explode('/', $pLink);
			$vRow = $this->getRow();
			foreach ($vParts as $vI => $vPart) {
				if('' != $vPart) {
					if(':' === $vPart[0]) {
						$vParts[$vI] = $vRow[substr($vPart,1)];
					}
				}
			}
						
			return implode('/', $vParts);
			*/
			return $this->replaceVars($pLink, '/');
		}
		
	}