<?php
/**
 * Extends the standard Zend_Pdf class to provide some extra handy functionality
 * @author Stephen Farrell <stephen@stephenfarrell.net>
 */
class SF_Pdf extends Zend_Pdf {
	const TEXT_ALIGN_LEFT = 'left';
	const TEXT_ALIGN_RIGHT = 'right';
	const TEXT_ALIGN_CENTER = 'center';
	
	/**
	* Extension of basic draw-text function to allow it to vertically center text
	*
	* @param Zend_Pdf_Page $page
	* @param string $text
	* @param int $x1
	* @param int $y1
	* @param int $x2
	* @param int $position
	* @param string $encoding
	* @return self
	*/
	public function drawText(Zend_Pdf_Page $page, $text, $x1, $y1, $x2 = null, $position = self::TEXT_ALIGN_LEFT, $encoding = null) {
		$bottom = $y1; // could do the same for vertical-centering
		switch ($position) {
			case self::TEXT_ALIGN_LEFT:
				$left = $x1;
				break;
			case self::TEXT_ALIGN_RIGHT:
				$text_width = $this->getTextWidth($text, $page->getFont(), $page->getFontSize());
				$left = $x1 - $text_width;
				break;
			case self::TEXT_ALIGN_CENTER:
				if (null === $x2) {
					throw new Exception("Cannot center text horizontally, x2 is not provided");
				}
				
				$text_width = $this->getTextWidth($text, $page->getFont(), $page->getFontSize());
				$box_width = $x2 - $x1;
				$left = $x1 + ($box_width - $text_width) / 2;
				break;
			default:
				throw new Exception("Invalid position value \"$position\"");
				break;
			}
		
		// display multi-line text
		foreach (explode(PHP_EOL, $text) as $i => $line) {
			$page->drawText($line, $left, $bottom - $i * $page->getFontSize(), $encoding);
		}
		
		return $this;
	}
	
	/**
	* Return length of generated string in points
	*
	* @param string $string
	* @param Zend_Pdf_Resource_Font $font
	* @param int $font_size
	* @return double
	*/
	public function getTextWidth($text, Zend_Pdf_Resource_Font $font, $font_size) {
		$drawing_text = iconv('', 'UTF-16BE', $text);
		$characters    = array();

		for ($i = 0; $i < strlen($drawing_text); $i++) {
			$characters[] = (ord($drawing_text[$i++]) << 8)  | ord ($drawing_text[$i]);
		}
	
		$glyphs        = $font->glyphNumbersForCharacters($characters);
		$widths        = $font->widthsForGlyphs($glyphs);
		$text_width   = (array_sum($widths) / $font->getUnitsPerEm()) * $font_size;
		
		return $text_width;
	}

	public static function splitTextAtMaxWidth($text, $maxWidth = 100) {
		$linesArray = array();
		$lineString = str_split($text,1);

		while(count($lineString) >= $maxWidth) {

			//make sure we dont split a word in 2 - check where the end of the last word is and split there
			$asString = implode('', $lineString);
			$splitAt = strrpos(substr($asString, 0, $maxWidth), ' ')+1;

			if(-1 == $splitAt) {
				$splitAt = 106;
			}

			unset($asString);

			$linesArray[] = implode('',array_splice($lineString, 0, $splitAt));

		}

		$linesArray[] = implode('',$lineString);

		return $linesArray;
	}
}	