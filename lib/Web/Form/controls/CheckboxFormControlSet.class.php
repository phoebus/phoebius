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
class CheckboxFormControlSet extends OptionFormControlSet
{
	/**
	 * @return CheckboxFormControlSet
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function __construct($name, $label)
	{
		parent::__construct($name, $label);

		// initialize correct internals
		$this->setDefaultValue(array());
	}

	function setDefaultValue($value)
	{
		if (!is_array($value)) {
			$value =
				$value
					? array($value)
					: array();
		}

		Assert::isTrue(
			sizeof($value) == array_intersect($this->getAvailableValues(), $value),
			'trying to set the default value that is out of options range'
		);

		parent::setDefaultValue($value);
	}

	function setImportedValue($value)
	{
		// remove unknown values
		$value = array_intersect($this->getAvailableValues(), $value);

		parent::setImportedValue($value);
	}

	function getControls()
	{
		$yield = array();
		$values = $this->getSelectedValues();
		$isImported = $this->isImported();

		foreach ($this->getOptions() as $value => $label) {
			$control = new CheckboxFormControl($this->getInnerName(), $label, $value);
			if (in_array($value, $values)) {
				if ($isImported)
					$control->importValue($value);
				else
					$control->setDefaultValue($value);
			}
		}

		return $yield;
	}
}

?>