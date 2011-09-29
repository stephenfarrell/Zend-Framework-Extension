<?php

	class SF_View_TableListing_Column_DateAndLink extends SF_View_TableListing_Column_Link {
		
		private $mDateFormat = 'M j, g:iA';
		private $mFriendlyDates = true;
		
		public function setDateFormat($pFormat) {
			$this->mDateFormat = $pFormat;
		}
		
		public function getDateFormat() {
			return $this->mDateFormat;
		}
		
		public function setFriendlyDates($pFriendlyDates) {
			$this->mFriendlyDates = $pFriendlyDates;
		}
		
		public function getFriendlyDates() {
			return $this->mFriendlyDates;
		}
		
		public function toString() {
			$vHtml = '<td' . ('' != $this->getClass() ? ' class="' . $this->getClass() . '"' : '') . '>';
			
			$vValue = $this->formatDate($this->getValue());
			
			$vLinkPath = $this->getLinkPath();
			
			$vTooltip = '';
			
			if('' != $this->getTooltip()) {
				$vTooltip = 'title="' . htmlentities($this->replaceVars($this->getTooltip(),' ')) . '"';
			}
			
			if('' != $vLinkPath) {
				$vHtml .= '<a href="' . $this->replaceLinkVars($vLinkPath) . '" ' . $vTooltip . ' >' . $vValue . '</a>';
			} else {
				$vHtml .= $vValue;
			}
			
			$vHtml .= '</td>';
			
			return $vHtml;
		}		
		
		public function formatDate($pDate) {
			
			$vDate = strtotime($pDate);
			
			$vDateNum = date('Ymd', $vDate);
			$vTime = date('g:i a', $vDate);
			$vUseFriendlyDates = $this->getFriendlyDates();
			$today = date("Ymd");
			$yesterday = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			if ($vUseFriendlyDates && $today == $vDateNum) {
				$vDateOutput = "Today, $vTime";
			} elseif ($vUseFriendlyDates && $yesterday == $vDateNum) {
				$vDateOutput = "Yesterday, $vTime";
			} else {
				$vDateOutput = date($this->getDateFormat(), $vDate);
			}			
			
			return $vDateOutput;
		}
	}