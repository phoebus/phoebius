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

abstract class LabeledFormControlSet extends FormControlSet
{
	private $ids = array();
	private $labels = array();

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

	function getAvailableValues()
	{
		return $this->ids;
	}

	function getSelectedValues()
	{
		Assert::isNotEmpty($this->ids, 'ids not yet set');

		$value = $this->getValue();
		return
			$value
				? array($value)
				: array();
	}

	function getUnselectedValues()
	{
		return array_diff($this->ids, $this->getSelectedValues());
	}

	final protected function spawnSingle()
	{
		Assert::isUnreachable('overridden');
	}
}

?>