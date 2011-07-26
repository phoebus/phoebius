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

class CheckboxFormControlSet extends FormControlSet
{
	private $ids = array();
	private $labels = array();

	function __construct($name, $label)
	{
		parent::__construct($name, $label);

		$this->setDistinct(true);
	}

	final function skipMissing($flag = true)
	{
		Assert::isUnreachable('nonsense');
	}

	final function isSkipsMissing()
	{
		return true;
	}

	final function skipWrong($flag = true)
	{
		Assert::isUnreachable('nonsense');
	}

	final function isSkipsWrong()
	{
		return true;
	}

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
		Assert::hasIndex($this->labels, $id, 'unable to find label for checkbox.id=%s', $id);

		return $this->labels[$id];
	}

	function setDefaultValue($value)
	{
		Assert::isTrue(is_array($value));
		Assert::isTrue(
			sizeof($value)
			== array_intersect($this->ids, $value)
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
		$value = array_intersect($this->ids, $value);

		// combine all ids (as non checked) and incoming (as checked)
		$allIds =
			array_replace(
				array_fill_keys($this->ids, null),
				array_combine($value, $value)
			);
		$controls = array();
		foreach ($allIds as $id) {
			$control = new OptionalValueFormControl($this->getInnerName(), $this->getLabelFor($id), $id);
			$control->importValue($id);

			Assert::isFalse($control->hasError());

			$controls[] = $control;
		}

		$this->setControls($controls);

		return !$this->hasError();
	}

	function getUncheckedValue()
	{
		return array_diff($this->ids, $this->getValue());
	}

	protected function makeDefaults()
	{
		$default = $this->getDefaultValue();
		$allIds =
			array_replace(
				array_fill_keys($this->ids, null),
				array_combine($default, $default)
			);
		$controls = array();
		foreach ($allIds as $id) {
			$control = new OptionalValueFormControl($this->getInnerName(), $this->getLabelFor($id), $id);
			$control->setDefaultValue($id);

			$controls[] = $control;
		}

		$this->setControls($controls);
	}

	final protected function spawnSingle()
	{
		Assert::isUnreachable('overridden');
	}
}

?>