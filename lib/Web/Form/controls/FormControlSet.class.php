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

// missing - means that value is not an array
// wrong - means there are inner errors
abstract class FormControlSet implements IFormControl
{
	const ID_PATTERN = '/^[a-z0-9_]+$/i';

	private $name;
	private $label;

	private $skipMissing = true;
	private $skipWrong = true;
	private $distinct = true;

	private $defaultValue = array();

	private $value = array();
	/**
	 * @var IFormControl[]
	 */
	private $controls = array();

	private $errorId;
	private $errorMessage;

	/**
	 * @return IFormControl
	 */
	abstract protected function spawnSingle();

	function __construct($name, $label)
	{
		Assert::isScalar($name);
		Assert::isTrue(preg_match(self::ID_PATTERN, $name));
		Assert::isScalar($label);

		$this->name = $name;
		$this->label = $label;
	}

	// sets are optional
	final function isOptional()
	{
		return true;
	}

	function skipWrong($flag = true)
	{
		Assert::isBoolean($flag);

		$this->skipWrong = $flag;

		return $this;
	}

	function isSkipsWrong()
	{
		return $this->skipWrong;
	}

	function skipMissing($flag = true)
	{
		Assert::isBoolean($flag);

		$this->skipMissing = $flag;

		return $this;
	}

	function isSkipsMissing()
	{
		return $this->skipMissing;
	}

	function setDistinct($flag = true)
	{
		Assert::isBoolean($flag);

		$this->distinct = $flag;

		return $this;
	}

	function isDistinct()
	{
		return $this->distinct;
	}

	function getName()
	{
		return $this->name;
	}

	function getInnerName()
	{
		return $this->name . '[]';
	}

	function getLabel()
	{
		return $this->label;
	}

	function getControls()
	{
		return $this->controls;
	}

	function getIterator()
	{
		return new ArrayIterator($this->controls);
	}

	function count()
	{
		return sizeof($this->controls);
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
		$this->controls = array();
		$this->value = array();
		$this->makeDefaults();
	}

	function getValue()
	{
		return $this->value;
	}

	function importValue($value)
	{
		$this->reset();

		if (is_array($value)) {
			$controls = array();

			foreach ($value as $innerValue) {
				if (!$innerValue && $this->isSkipsMissing())
					continue;

				$control = $this->spawnSingle();

				// if import failed...
				if (!$control->importValue($innerValue)) {
					if (
						// if we can skip the specified error - do this
							($control->isMissing() && $this->isSkipsMissing())
							|| ($control->isWrong() && $this->isSkipsWrong())
					) {
						continue;
					}
					else { // otherwise mark the surrounding control as wrong
						$this->markWrong();
					}
				}

				$controls[] = $control;
			}

			$this->setControls($controls);
		}
		else if (!empty($value) && !is_array($value)) {
			$this->markMissing();
		}

		return !$this->hasErrors();
	}

	function setDefaultValue($value)
	{
		$this->defaultValue = $value;
		$this->makeDefaults();

		return $this;
	}

	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	function toHtml(array $htmlAttributes = array())
	{
		$s = '';
		foreach ($this->controls as $control) {
			$s .= $control->toHtml($htmlAttributes);
		}

		return $s;
	}

	protected function markMissing($message = null)
	{
		$this->errorId = FormControlError::missing();
		$this->errorMessage = $message;
	}

	protected function markWrong($message = null)
	{
		$this->errorId = FormControlError::wrong();
		$this->errorMessage = $message;
	}

	protected function setControls(array $controls)
	{
		$value = array();
		foreach ($controls as $control) {
			if (($_ = $control->getValue()))
				$value[] = $_;
		}

		if ($this->isDistinct()) {
			$value = array_unique($value);
		}

		$this->value = $value;
		$this->controls = $controls;

		return $this;
	}

	protected function makeDefaults()
	{
		$controls = array();
		foreach ($this->defaultValue as $value) {
			$control = $this->spawnSingle();
			$control->setDefaultValue($value);

			$controls[] = $control;
		}

		$this->setControls($controls);
	}
}

?>