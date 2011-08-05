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
 * Abstract control where a value should be in the specified range. See setLabels()
 * @ingroup Form
 */
abstract class SetFormControl extends FormControlScalar
{
	private $ids = array();
	private $labels = array();

	/**
	 * Sets the value=>label set
	 * @param array $labels
	 * @return SetFormControl
	 */
	function setLabels(array $labels)
	{
		$this->ids = array_keys($labels);
		$this->labels = $labels;

		return $this;
	}

	/**
	 * Gets the set of values and their labels
	 * @return array
	 */
	function getLabels()
	{
		return $this->labels;
	}

	/**
	 * Gets the label for the custom id
	 * @param  $id
	 * @return array
	 */
	function getLabelFor($id)
	{
		Assert::hasIndex($this->labels, $id, 'unable to find label for %s', $id);

		return $this->labels[$id];
	}

	/**
	 * Gets the list of possible values
	 * @return array
	 */
	function getAvailableValues()
	{
		return $this->ids;
	}

	/**
	 * Gets the list of imported/default values
	 * @return array
	 */
	function getSelectedValues()
	{
		Assert::isNotEmpty($this->ids, 'options not yet set');

		$value = $this->getValue();
		if ($value) return array($value);
		else return array();
	}

	/**
	 * Gets the list of not-selected values (that were not imported or set as default)
	 * @return array
	 */
	function getUnselectedValues()
	{
		return array_diff($this->ids, $this->getSelectedValues());
	}

	/**
	 * Gets the list of html <option> tags
	 * @return array
	 */
	protected function getOptions()
	{
		$yield = array();
		$allIds =
			array_replace(
				array_fill_keys($this->getAvailableValues(), false),
				array_fill_keys($this->getSelectedValues(), true)
			);
		foreach ($allIds as $id => $selected) {
			$yield[] = $this->getOption($id, $selected);
		}

		return $yield;
	}

	/**
	 * Gets the <option> tag as string
	 * @param  $value
	 * @param  $selected
	 * @return string
	 */
	protected function getOption($value, $selected)
	{
		Assert::isScalarOrNull($value);
		Assert::isBoolean($selected);

		$attributes = array();

		if ($value) {
			$attributes['value'] = $value;
		}

		if ($selected) {
			$attributes['selected'] = 'selected';
		}

		return HtmlUtil::getContainer('option', $attributes, $this->getLabelFor($value));
	}
}

?>