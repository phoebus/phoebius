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

abstract class OptionalValueFormControl extends InputFormControl
{
	private $value;

	function __construct($name, $label, $value)
	{
		Assert::isScalar($value);

		$this->value = $value;

		parent::__construct($name, $label);
	}

	final function getFixedValue()
	{
		return $this->value;
	}

	final function isOptional()
	{
		return true;
	}

	final function markRequired()
	{
		Assert::isUnreachable('control is hard-coded optional');
	}

	final function markMissing($message = null)
	{
		Assert::isUnreachable('control can be missing, it is ok');
	}

	function setValue($value)
	{
		if ($value !== null && $value != $this->value) {
			$this->markWrong('unexpected control value');
		}
		else {
			parent::setValue($value);
		}
	}
}

?>