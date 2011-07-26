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

class RadioFormControlSet extends LabeledFormControlSet
{
	function setDefaultValue($value)
	{
		Assert::isScalarOrNull($value);
		if ($value)
			Assert::isTrue(in_array($value, $this->getAvailableValues()));

		parent::setDefaultValue($value);
	}

	function importValue($value)
	{
		if ($value && !is_scalar($value)) {
			$this->markMissing('not a scalar');
			return false;
		}

		if (!in_array($value, $this->getAvailableValues())) {
			$value = $this->getDefaultValue();
		}

		// combine all ids (as non checked) and incoming (as checked)
		$allIds = array_fill_keys($this->getAvailableValues(), null);
		$allIds[$value] = $value;
		$controls = array();
		foreach ($allIds as $id => $value) {
			$control = new RadioFormControlSet($this->getInnerName(), $this->getLabelFor($id), $id);
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
		$allIds = array_fill_keys($this->getAvailableValues(), null);
		$allIds[$default] = $default;
		$controls = array();
		foreach ($allIds as $id => $value) {
			$control = new RadioFormControl($this->getInnerName(), $this->getLabelFor($id), $id);
			$control->setDefaultValue($value);

			$controls[] = $control;
		}

		$this->setControls($controls);
	}
}

?>