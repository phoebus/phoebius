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
/**
 * Represents a control set.
 *
 * Caveats for the set:
 *  - "missing" means that incoming value is not of a valid type (not an array)
 *  - "wrong" means there are import errors withing inner controls (if not supressed)
 *  - set is always optional as a control in case when user didn't selected anything in a set
 *  - inner control is a control collected by the set
 *
 * @ingroup Form
 */
abstract class FormControlSet implements IFormControl, IteratorAggregate, Countable
{
	const ID_PATTERN = '/^[a-z0-9_]+$/i';

	const ERROR_INVALID_VALUE = 'incoming value is not of a valid type';
	const ERROR_HAS_INNER_ERRORS = 'inner controls have import errors';

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
	 * Gets the instance of inner control
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

	final function isOptional()
	{
		return true;
	}

	/**
	 * Makes control to skip wrong inner values
	 * @param bool $flag
	 * @return FormControlSet
	 */
	function skipWrong($flag = true)
	{
		Assert::isBoolean($flag);

		$this->skipWrong = $flag;

		return $this;
	}

	/**
	 * Determines whether a control set skips wrong inner values
	 * @return bool
	 */
	function isSkipsWrong()
	{
		return $this->skipWrong;
	}

	/**
	 * Makes control to skip missing/empty inner values
	 * @param bool $flag
	 * @return FormControlSet
	 */
	function skipMissing($flag = true)
	{
		Assert::isBoolean($flag);

		$this->skipMissing = $flag;

		return $this;
	}


	/**
	 * Determines whether a control set skips missing/empty inner values
	 * @return bool
	 */
	function isSkipsMissing()
	{
		return $this->skipMissing;
	}

	/**
	 * Makes control to import only distinct set of inner values
	 * @param bool $flag
	 * @return FormControlSet
	 */
	function setDistinct($flag = true)
	{
		Assert::isBoolean($flag);

		$this->distinct = $flag;

		return $this;
	}

	/**
	 * Determines whether a control set imports only distinct set of inner values
	 * @return bool
	 */
	function isDistinct()
	{
		return $this->distinct;
	}

	function getName()
	{
		return $this->name;
	}

	/**
	 * Gets the name for inner controls
	 * @return string
	 */
	function getInnerName()
	{
		return $this->name . '[]';
	}

	function getLabel()
	{
		return $this->label;
	}

	/**
	 * Gets the inner controls
	 * @return IFormControl[]
	 */
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
						$this->markWrong(self::ERROR_HAS_INNER_ERRORS);
					}
				}

				$controls[] = $control;
			}

			$this->setControls($controls);
		}
		else if (!empty($value) && !is_array($value)) {
			$this->markMissing(self::ERROR_INVALID_VALUE);
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

	/**
	 * Marks control as failed during import because of a missing value
	 * @param string $message
	 * @return void
	 */
	protected function markMissing($message = null)
	{
		$this->errorId = FormControlError::missing();
		$this->errorMessage = $message;
	}

	/**
	 * Marks control as failed during import because of a wrong value
	 * @param string $message
	 * @return void
	 */
	protected function markWrong($message = null)
	{
		$this->errorId = FormControlError::wrong();
		$this->errorMessage = $message;
	}

	/**
	 * Sets inner controls and marks the value as imported. This is called by IFormControl::importValue()
	 * where all checks are performed
	 * @param array $controls
	 * @return void
	 */
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

	/**
	 * Creates inner controls for default value
	 * @return void
	 */
	protected function makeDefaults()
	{
		$controls = array();
		foreach ($this->getDefaultValue() as $value) {
			$control = $this->spawnSingle();
			$control->setDefaultValue($value);

			$controls[] = $control;
		}

		$this->setControls($controls);
	}
}

?>