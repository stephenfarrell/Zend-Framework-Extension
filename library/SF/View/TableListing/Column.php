<?php
	class SF_View_TableListing_Column {
		
		protected $mDisplay = true; // show or hide the column
		protected $mValue = ''; // raw value from the sql
		protected $mTitle = '';
		
		protected $mValuePrefix = '';
		protected $mValueSuffix = '';
		protected $mValueSuffixPlural = '';
		
		protected $mClass = null;
		
		protected $mSelectAlias;
		
		protected $mAccessorName = NULL;
		
		protected $mImagePath = NULL;
		
		protected $mImageName = NULL;
		
		protected $mTooltip = NULL;
		
		protected $mTruncateLength = NULL;
		
		protected $mNewLines = true;
		
		protected $mHideRepeatingValues = false;
		
		const TOTAL_NONE = 0;
		const TOTAL_SUM = 1;
		const TOTAL_AVG = 2;
		const TOTAL_MIN = 3;
		const TOTAL_MAX = 4;
		
		private $mTotal = 0;
		
		//Kludge: this is nasty but i cant think of another way to do it at the moment
		private $mRow = array();
		private $mPreviousRow = array();
		private $rowNum = 0;
		
		public function __construct($pAlias = null, $pTitle = null, $pVisible = true, $accessor = null) {
			$this->setTitle($pTitle);
			$this->setVisible($pVisible);
			$this->setAlias($pAlias);

			$this->setAccessorName($accessor);
			
			return $this;
		}
		
		public function setNewLines($pNewLines) {
			$this->mNewLines = $pNewLines;
		}
		
		public function getNewLines() {
			return $this->mNewLines;
		}
		
		public function setAlias($pAlias) {
			$this->mSelectAlias = $pAlias;
			
			return $this;
		}
		
		public function getAlias() {
			return $this->mSelectAlias;
		}
		
		public function setAccessorName($pAccessor) {
			$this->mAccessorName = $pAccessor;
			
			return $this;
		}
		
		public function getAccessorName() {
			return $this->mAccessorName;
		}
		
		public function setTitle($pTitle) {
			$this->mTitle = $pTitle;
			
			return $this;
		}
		
		public function getTitle() {
			return $this->mTitle;
		}
		
		public function setTooltip($pTooltip) {
			$this->mTooltip = $pTooltip;
		}
		
		public function getTooltip() {
			return $this->mTooltip;
		}		
		
		public function setValue($pValue) {
			$this->mValue = $pValue;
			
			return $this;
		}
		
		public function setTruncateLength($pTrunc) {
			$this->mTruncateLength = $pTrunc;
		}
		
		public function getTruncateLength() {
			return $this->mTruncateLength;
		}
		
		public function setTotal($pTotal) {
			$this->mTotal = $pTotal;
		}
		
		public function getTotal() {
			return $this->mTotal;
		}

		/**
		 * Get the value to be displayed in the column
		 *
		 * @return unknown
		 */
		public function getValue() {
			$vReturn = NULL;
			
			$vTruncatedValue = $this->mValue;
			
			if($this->getTruncateLength() !== null) {
				if(strlen($vTruncatedValue) > $this->getTruncateLength()) {
					$vTruncatedValue = substr($vTruncatedValue, 0, $this->getTruncateLength()-2) . '...';
				}
			}
  			
			if(!empty($this->mValue)) {
				$vSuffix=  $this->getValueSuffix();
				
				if($this->mValueSuffixPlural) {
					if(is_numeric($this->mValue)) {
						if($this->mValue > 1) {
							$vSuffix = $this->mValueSuffixPlural;
						}
					}
				}
				
				$vReturn = $this->getValuePrefix() . $vTruncatedValue . $vSuffix;
			}
			
			return $vReturn;
		}
		
		public function getOtherValue($pAlias) {
			$vValue = NULL;
			if(isset($this->mRow[$pAlias])) {
				$vValue = $this->mRow[$pAlias];
			}
			
			return $vValue;
		}
		
		public function setValuePrefix($pValuePrefix) {
			$this->mValuePrefix = $pValuePrefix;
			
			return $this;
		}
		
		public function getValuePrefix() {
			return $this->mValuePrefix;
		}
		
		public function setValueSuffix($pValueSuffix, $pPlural = null) {
			$this->mValueSuffix = $pValueSuffix;
			$this->mValueSuffixPlural = $pPlural;
			
			return $this;
		}
		
		public function getValueSuffix() {
			return $this->mValueSuffix;
		}
		
		public function setImagePath($pPath, $pImageName = false) {
			$this->mImagePath = $pPath;
			$this->mImageName = $pImageName;
		}
		
		public function getImagePath() {
			return $this->mImagePath;
		}		
		
		public function getImageName() {
			return $this->mImageName;
		}
		
		
		public function setRow($pRow) {
			$this->mRow = $pRow;
			
			return $this;
		}
		
		public function getRow() {
			return $this->mRow;
		}
		
		public function setPreviousRow($pRow) {
			$this->mPreviousRow = $pRow;
		}
		
		public function getPreviousRow() {
			return $this->mPreviousRow;
		}
		
		public function setHideRepeatingValues($pHide = true) {
			$this->mHideRepeatingValues =true;
		}
		
		public function getHideRepeatingValues() {
			return $this->mHideRepeatingValues;
		}
		
		public function setVisible($pVisible = true) {
			$this->mDisplay = $pVisible;
			
			return $this;
		}
		
		public function getVisible() {
			return $this->mDisplay;
		}
		
		public function setClass($pClass) {
			$this->mClass = $pClass;
			
			return $this;
		}
		
		public function getClass() {
			return $this->mClass;
		}

		public function getRowNum() {
			return $this->rowNum;
		}

		public function setRowNum($rowNum) {
			$this->rowNum = $rowNum;

			return $this;
		}
		
		public function toString() {
			if($this->getNewLines()) {
				$vValue = nl2br($this->getValue());
			} else {
				$vValue = $this->getValue();
			}
			
			
			if($this->getHideRepeatingValues()) {			
				$vPrevRow = $this->getPreviousRow();
				$vPrevValue = null;

				if($vPrevRow) {
					$vPrevValue = $vPrevRow[$this->getAlias()];
					if($this->getNewLines()) {
						$vPrevValue = nl2br($vPrevValue);
					}
				}

				if($vValue == $vPrevValue) {
					$vValue = '';
				}
			}
			
			
			$vTooltip = '';
			
			
			if(NULL != $this->getTooltip()) {
				$vTooltip = 'title="' . htmlentities($this->replaceVars($this->getTooltip())) . '"';
			}
			
			if(NULL != $this->getImagePath()) {
				$vImagePath = $this->replaceVars($this->getImagePath(), '/');
				
				//an image name was given
				if($this->getImageName() !== FALSE) {
					$vImageName = $this->replaceVars($this->getImageName(),'/');
					if($vImageName) {
						$vImagePath .= $vImageName;
					} else {
						// a name was given but its null, dont show an image
						$vImagePath = NULL;
					}
				}
				if($vImagePath) {
					$vValue = '<img src="' . $vImagePath . '" alt="' . $vValue . '" class="no-border" ' . $vTooltip . ' />';
				}
			}
			
			$vHtml = '<td' . ('' != $this->getClass() ? ' class="' . $this->getClass() . '"' : '') . '>' . $vValue . '</td>';
			
			return $vHtml;
		}
		
		public function replaceVars($pText, $pDelimiter = ' ', $pRow = null) {
			$vParts = explode($pDelimiter, $pText);
			if(null !== $pRow) {
				$vRow = $pRow;
			} else {
				$vRow = $this->getRow();
			}

			foreach ($vParts as $vI => $vPart) {
				if('' != $vPart) {
					if(':' === $vPart[0]) {
						// we know where to start, look for fullstops as we need to stop when we hit one
						$vVarName = substr($vPart,1);
						$vStopPos = strpos($vVarName, '.');
						//echo '<pre>';print_r($vRow); echo '</pre>';
						//see if we hit a fullstop
						if(false === $vStopPos) {
							//no fullstop, just replace the lot

							$vParts[$vI] = $vRow[trim($vVarName)];
						} else {
							$vStoppedVarName = substr($vVarName,0,$vStopPos);
							
							$vParts[$vI] = $vRow[$vStoppedVarName] . substr($vVarName, $vStopPos); //tack the bit after the fullstop back on
						}
						
						
					}
				}
			}
			
			return implode($pDelimiter, $vParts);
		}		
	}