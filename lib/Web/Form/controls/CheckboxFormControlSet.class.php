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
 * Represents a checkbox set
 * @ingroup Form
 */
class CheckboxFormControlSet extends LabeledFormControlSet
{
	/**
	 * @return CheckboxFormControlSet
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function getSelectedValues()
	{
		return $this->getValue();
	}

	function setDefaultValue($value)
	{
		Assert::isTrue(is_array($value));
		Assert::isTrue(
			sizeof($value)
			== array_intersect($this->getAvailableValues(), $value)
		);

		parent::setDefaultValue($value);
	}

	function importValue($value)
	{
		if (!is_array($value)) {
			$this->markMissing('not an array');
			return false;
		}

		// remove unknown values
		$value = array_intersect($this->getAvailableValues(), $value);

		// combine all ids (as non checked) and incoming (as checked)
		$allIds =
			array_replace(
				array_fill_keys($this->getAvailableValues(), null),
				array_combine($value, $value)
			);
		$controls = array();
		foreach ($allIds as $id => $value) {
			$control = new CheckboxFormControl($this->getInnerName(), $this->getLabelFor($id), $id);
			$control->importValue($value);

			Assert::isFalse($control->hasError());

			$controls[] = $control;
		}

		$this->setControls($controls);

		return !$this->hasError();
	}

	protected function makeDefaults()
	{
		$default = $this->getDefaultValue();
		$allIds =
			array_replace(
				array_fill_keys($this->getAvailableValues(), null),
				array_combine($default, $default)
			);
		$controls = array();
		foreach ($allIds as $id => $value) {
			$control = new CheckboxFormControl($this->getInnerName(), $this->getLabelFor($id), $id);
			$control->setDefaultValue($value);

			$controls[] = $control;
		}

		$this->setControls($controls);
	}
}

?>