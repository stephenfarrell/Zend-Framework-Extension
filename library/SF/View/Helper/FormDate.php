<?php

class SF_View_Helper_FormDate extends Zend_View_Helper_FormElement
{
	public function formDate ($name, $value = null, $attribs = null)
	{
		// if the element is rendered without a value,
		// show today's date

//		if ($value === null)
//		{
//			$value = date('Y-m-d');
//		}

		if ($value === null) {
			$year = $month = $day = null;
		} else {
			list($year, $month, $day) = explode('-', $value);
		}

		// build select options

		$date = new Zend_Date();

		$dayOptions = array();
		for ($i = 1; $i < 32; $i ++)
		{
			$idx = str_pad($i, 2, '0', STR_PAD_LEFT);
			$dayOptions[$idx] = str_pad($i, 2, '0', STR_PAD_LEFT);
		}

		$monthOptions = array();
		for ($i = 1; $i < 13; $i ++)
		{
			$date->set($i, Zend_Date::MONTH);
			$idx = str_pad($i, 2, '0', STR_PAD_LEFT);
			$monthOptions[$idx] = $date->toString('MMMM');
		}

		$yearOptions = array();
		for ($i = 1970; $i < 2031; $i ++)
		{
			$yearOptions[$i] = $i;
		}

		// return the 3 selects separated by -
		if(!isset($attribs['class'])) {
			$attribs['class'] = 'date-field';
		}
		return
			$this->view->formText( //formSelect
				$name . '_day',
				$day,
				array_merge($attribs,array('maxlength'=>2)),
				$dayOptions) . ' / ' .
			$this->view->formText(
				$name . '_month',
				$month,
				array_merge($attribs,array('maxlength'=>2)),
				$monthOptions) . ' / ' .
			$this->view->formText(
				$name . '_year',
				$year,
				array_merge($attribs,array('maxlength'=>4)),
				$yearOptions
			). '<span class="date-field">&nbsp;(dd/mm/yyyy)</span>';
	}
}