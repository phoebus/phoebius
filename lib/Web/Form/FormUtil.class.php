<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

/**
 * @ingroup Form
 */
class FormUtil
{
	static function dump(Form $form)
	{
		return
				$form->getHeadHtml(
					array(
						'style' => 'border:1px solid #000;padding:10px;margin:10px;'
									. ($form->hasFormErrors() ? 'border-color:red;' : '')
					)
				)
				. '<h3>' . $form->getId() . '</h3>'
				. self::dumpValues($form)
				. self::dumpErrors($form)
				. self::dumpControls($form)
				. $form->getHeelHtml();
	}

	static function dumpValues(Form $form)
	{
		return
				'<h4 onclick="var e = document.getElementById(\''.$form->getId().'\'); e.style.display = e.style.display == \'none\' ? \'block\' : \'none\'" style="border-bottom:1px dashed #000;">Dump</h4>'
				. '<pre id="'.$form->getId().'">'
				. print_r($form->export(), true)
				. '</pre>';
	}

	static function dumpControls(Form $form)
	{
		$s = '<dl>';

		foreach ($form->getControls() as $control) {
			$s .= self::dumpControl($control);
		}

		$s .= '</dl>';

		foreach ($form->getControls() as $control) {
			if ($control instanceof ButtonFormControl)
				$s .= $control->toHtml();
		}

		return $s;
	}

	static function dumpControl(IFormControl $control)
	{
		if ($control instanceof HiddenFormControl || $control instanceof HiddenFormControlSet) return;
		if ($control instanceof ButtonFormControl) return;
		if ($control instanceof CheckboxFormControl) return self::dumpCb($control);
		if ($control instanceof FormControlScalar) return self::dumpScalar($control);
		if ($control instanceof FormControlSet) return self::dumpSet($control);

		Assert::isUnreachable('don`t know how to dump %s', get_class($control));
	}

	static private function dumpSet(FormControlSet $control)
	{
		$inner = '';
		foreach ($control as $innerControl) {
			$inner .= self::dumpScalar($innerControl);
		}

		return <<<EOT
<dt>{$control->getLabel()}</dt>
<dd>
	<dl>{$inner}</dl>
</dd>
EOT;
	}

	static private function dumpScalar(FormControlScalar $control)
	{
		return <<<EOT
<dt>{$control->getLabel()}</dt>
<dd>{$control->toHtml(
			$control->hasError()
					? array('style' => 'border:1px solid red;')
					: array()
		)}</dd>
EOT;
	}

	static private function dumpCb(CheckboxFormControl $control)
	{
		return <<<EOT
<dt>{$control->toHtml(
			$control->hasError()
					? array('style' => 'border:1px solid red;')
					: array()
		)}</dt>
<dd>{$control->getLabel()}</dd>
EOT;
	}

	static function dumpErrors(Form $form)
	{
		if (!$form->hasErrors()) return '';

		$s = '<ul>';

		foreach ($form->getErrors() as $id => $error) {
			$s .= '<li>' . $id . ': ' . $error . '</li>';
		}

		foreach ($form->getControls() as $control) {
			$s .= self::dumpControlError($control);
		}


		$s .= '</ul>';

		return $s;
	}

	static private function dumpControlError(IFormControl $control)
	{
		if (!$control->hasError()) return '';

		if ($control instanceof FormControlSet && $control->hasError(FormControlError::WRONG)) {
			$message = '<ul>';
			foreach ($control as $innerControl) {
				$message .= self::dumpControlError($innerControl);
			}
			$message .= '</ul>';
		}
		else {
			$message = $control->getErrorMessage();
		}

		return
			'<li>' . $control->getName() . (($label = $control->getLabel()) ? " ($label)" : '')
			. ' is ' . $control->getError()
			. ': <i>' . $message . '</i></li>';
	}
}

?>