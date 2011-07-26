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

abstract class FormControlScalar implements IFormControl
{
	private $name;
	private $label;
	private $isOptional;

	private $isImported;
	private $defaultValue;
	private $importedValue;

	private $errorId;
	private $errorMessage;

	function __construct($name, $label)
	{
		Assert::isScalar($name);
		Assert::isScalar($label);

		$this->name = $name;
		$this->label = $label;
	}

	function isOptional()
	{
		return $this->isOptional;
	}

	function markOptional()
	{
		$this->isOptional = true;

		return $this;
	}

	function markRequired()
	{
		$this->isOptional = false;

		return $this;
	}

	function getName()
	{
		return $this->name;
	}

	function getLabel()
	{
		return $this->label;
	}

	function getValue()
	{
		return
			$this->isImported
				? $this->importedValue
				: $this->defaultValue;
	}

	function importValue($value)
	{
		$this->reset();

		if ($value && !is_scalar($value)) {
			$this->markMissing('not a scalar value given');
		}
		else {
			$this->setValue($value);
		}

		return !$this->hasError();
	}

	function setDefaultValue($value)
	{
		Assert::isScalarOrNull($value);

		$this->defaultValue = $value;

		return $this;
	}

	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	function hasError()
	{
		return !!$this->errorId;
	}

	function isMissing()
	{
		return
			($this->errorId && $this->errorId->is(FormControlError::MISSING))
				? ($this->errorMessage ? $this->errorMessage : true)
				: false;
	}

	function isWrong()
	{
		return
			($this->errorId && $this->errorId->is(FormControlError::WRONG))
				? ($this->errorMessage ? $this->errorMessage : true)
				: false;
	}

	function reset()
	{
		$this->errorId = null;
		$this->errorMessage = null;
		$this->isImported = false;
		$this->importedValue = null;
	}

	function markMissing($message = null)
	{
		$this->errorId = FormControlError::missing();
		$this->errorMessage = $message;
	}

	function markWrong($message = null)
	{
		$this->errorId = FormControlError::wrong();
		$this->errorMessage = $message;
	}

	protected function setValue($value)
	{
		$this->isImported = true;
		$this->importedValue = $value;

		return $this;
	}
}

?>