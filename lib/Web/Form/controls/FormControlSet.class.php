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
 * Represents a control set.
 *
 * Caveats for the set:
 *  - "missing" means nothing - set cannot be empty by design
 *  - "invalid" means what is should - scope value has wrong type, unable even to try the import
 *  - "wrong" means there are import errors withing inner controls (if not supressed). You may disable importing of such
 *  - set is always optional as a control in case when user didn't selected anything in a set
 *  - inner control is a control collected by the set
 *
 * @ingroup Form
 */
abstract class FormControlSet extends BaseFormControl implements IteratorAggregate, Countable
{
	const ID_PATTERN = '/^[a-z0-9_]+$/i';

	/**
	 * @var bool
	 */
	private $importMissing = false;

	/**
	 * @var bool
	 */
	private $importWrong = false;

	/**
	 * @var bool
	 */
	private $importDistinct = true;

	/**
	 * @var IFormControl[]
	 */
	private $controls = array();

	/**
	 * Gets the instance of inner control
	 * @return IFormControl
	 */
	abstract protected function spawnSingle();

	function __construct($name, $label)
	{
		Assert::isTrue(preg_match(self::ID_PATTERN, $name));

		parent::__construct($name, $label);
	}

	function getValue()
	{
		$value = parent::getValue();
        return
			$value
                ? $value
                : array();
	}

	/**
	 * Overridden. Set is always optional as can consist of 0 elements by design.
	 * @return bool
	 */
	final function isOptional()
	{
		return true;
	}

	/**
	 * Makes control to skip missing/empty inner values
	 * @param bool $flag
	 * @return FormControlSet
	 */
	function enableImportMissing($flag = true)
	{
		Assert::isBoolean($flag);

		$this->importMissing = $flag;

		return $this;
	}


	/**
	 * Determines whether a control set skips missing/empty inner values
	 * @return bool
	 */
	function importsMissing()
	{
		return $this->importMissing;
	}

	/**
	 * Makes control to import wrong
	 * @return FormControlSet
	 */
	function enableImportWrong($flag = true)
	{
		Assert::isBoolean($flag);

		$this->importWrong = $flag;

		return $this;
	}

	/**
	 * Determines whether a control set skips wrong inner values
	 * @return bool
	 */
	function importsWrong()
	{
		return $this->importWrong;
	}

	/**
	 * Makes control to import only distinct set of inner values
	 * @param bool $flag
	 * @return FormControlSet
	 */
	function enableImportDistinct($flag = true)
	{
		Assert::isBoolean($flag);

		$this->importDistinct = $flag;

		return $this;
	}

	/**
	 * Determines whether a control set imports only distinct set of inner values
	 * @return bool
	 */
	function importsDistinct()
	{
		return $this->importDistinct;
	}

	/**
	 * Gets the name for inner controls
	 * @return string
	 */
	function getInnerName()
	{
		return $this->getName() . '[]';
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
		return new ArrayIterator($this->getControls());
	}

	function count()
	{
		return sizeof($this->controls);
	}

	function importValue($value)
	{
		if (is_array($value)) {
			$controls = array();
			$takenValues = array(); // track distinct

			foreach ($value as $innerValue) {
				if (!$innerValue && !$this->importsMissing())
					continue;

				$control = $this->spawnSingle();
				$control->importValue($innerValue);

				// if import failed...
				if ($control->hasError()) {
					$error = $control->getError();
					if (
						// if we can skip the specified error - do this
							($error->is(FormControlError::MISSING) && !$this->importsMissing())
							|| ($error->is(FormControlError::WRONG) && !$this->importsWrong())
							|| $error->is(FormControlError::INVALID)
					) {
						continue;
					}
					else { // otherwise mark the surrounding control as wrong
						$this->setError(FormControlError::wrong());
					}
				}
				else if ($this->importsDistinct()) {
					$importedValue = $control->getValue();
					if (in_array($importedValue, $takenValues)) {
						continue;
					}
					else {
						$takenValues[] = $control->getValue();
					}
				}

				$controls[] = $control;
			}

			$this->setControls($controls);
		}
		else if ($value && !is_array($value)) {
			$this->setError(FormControlError::invalid());
		}

		return !$this->hasError();
	}

	function getDefaultValue()
	{
		$value = parent::getDefaultValue();
		return $value
				? $value
				: array();
	}

	function setDefaultValue($value)
	{
		if (!is_array($value)) {
			$value =
				$value
					? array($value)
					: array();
		}

		parent::setDefaultValue($value);
		$this->makeDefaults();

		return $this;
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
	 * Sets inner controls and marks the value as imported. This is called by IFormControl::importValue()
	 * where all checks are performed
	 * @param IFormControl[] $controls
	 * @return void
	 */
	protected function setControls(array $controls)
	{
		$value = array();
		$this->controls = array();

		foreach ($controls as $control) {
			$value[] = $control->getValue();
			$this->controls[] = $control;
		}

		parent::setImportedValue($value);
	}

	protected function dropImportedValue()
	{
		$this->makeDefaults();
		parent::dropImportedValue();
	}

	/**
	 * Creates inner controls for default value
	 * @return void
	 */
	protected function makeDefaults()
	{
		$this->controls = array();

		foreach ($this->getDefaultValue() as $value) {
			$this->controls[] = $this->spawnSingle()->setDefaultValue($value);
		}
	}

	protected function setError(FormControlError $error)
	{
		Assert::isFalse($error->is(FormControlError::MISSING), '%s cannot be missing', get_class($this));

		return parent::setError($error);
	}
}

?>