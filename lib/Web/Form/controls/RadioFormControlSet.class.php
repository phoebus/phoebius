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
 * Radio button group
 * @ingroup Form
 */
class RadioFormControlSet extends OptionFormControlSet
{
	private $defaultValue;
	private $importedValue;

	/**
	 * @return RadioFormControlSet
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function setDefaultValue($value)
	{
		Assert::isScalar($value, 'default value shall be scalar');
		Assert::isTrue(in_array($value, $this->getAvailableValues()));

		$this->defaultValue = $value;

		return $this;
	}

	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	protected function setImportedValue($value)
	{
		$this->importedValue = $value;

		parent::setImportedValue($value);
	}

	protected function getImportedValue()
	{
		return $this->importedValue;
	}

	function getSelectedValues()
	{
		$value = $this->getValue();
		return
				$value
					? array($value)
					: array();
	}

	function importValue($value)
	{
		if ($value && !is_scalar($value)) {
			$this->setError(FormControlError::invalid());
			$value =
				$this->getError()->getBehaviour()->is(FormControlErrorBehaviour::USE_DEFAULT)
					? $this->getDefaultValue()
					: null;
		}

		if (!in_array($value, $this->getAvailableValues())) {
			$this->setError(FormControlError::invalid());
			$value =
				$this->getError()->getBehaviour()->is(FormControlErrorBehaviour::USE_DEFAULT)
					? $this->getDefaultValue()
					: null;
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