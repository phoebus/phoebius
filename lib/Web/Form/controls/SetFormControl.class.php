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

// select
// multi-select
abstract class SetFormControl extends FormControlScalar
{
	private $ids = array();
	private $labels = array();

	function setLabels(array $labels)
	{
		$this->ids = array_keys($labels);
		$this->labels = $labels;

		return $this;
	}

	function getLabels()
	{
		return $this->labels;
	}

	function getLabelFor($id)
	{
		Assert::hasIndex($this->labels, $id, 'unable to find label for %s', $id);

		return $this->labels[$id];
	}

	function getAvailableValues()
	{
		return $this->ids;
	}

	function getSelectedValues()
	{
		Assert::isNotEmpty($this->ids, 'ids not yet set');

		return $this->getValue();
	}

	function getUnselectedValues()
	{
		return array_diff($this->ids, $this->getSelectedValues());
	}

	protected function getOptions()
	{
		$yield = array();
		$allIds =
			array_replace(
				array_fill_keys($this->getAvailableValues(), false),
				array_fill_keys($this->getSelectedValues(), true)
			);
		foreach ($allIds as $id => $selected) {
			$yield = $this->getOption($id, $selected);
		}

		return $yield;
	}

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