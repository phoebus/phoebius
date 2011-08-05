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
 * Represents a control set of controls that have custom labels inside
 * @ingroup Form
 */
abstract class OptionFormControlSet extends FormControlSet
{
	private $values = array();
	private $options = array();

	/**
	 * Sets the value=>label set
	 * @param array $labels
	 * @return LabeledFormControlSet
	 */
	function setOptions(array $options)
	{
		$this->values = array_keys($options);
		$this->options = $options;

		return $this;
	}

	/**
	 * Gets the set of values and their labels
	 * @return array
	 */
	function getOptions()
	{
		return $this->options;
	}

	/**
	 * Gets the label for the custom id
	 * @param  $id
	 * @return array
	 */
	function getLabelFor($value)
	{
		Assert::hasIndex($this->options, $value, 'unable to find label for value=%s', $value);

		return $this->options[$value];
	}

	/**
	 * Gets the list of possible values
	 * @return array
	 */
	function getAvailableValues()
	{
		return $this->values;
	}

	/**
	 * Gets the list of imported/default values
	 * @return array
	 */
	function getSelectedValues()
	{
		return $this->getValue();
	}

	/**
	 * Gets the list of not-selected values (that were not imported or set as default)
	 * @return array
	 */
	function getUnselectedValues()
	{
		return array_diff($this->values, $this->getSelectedValues());
	}
}

?>