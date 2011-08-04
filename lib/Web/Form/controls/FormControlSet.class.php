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
	const NAME_PATTERN = '/^[a-z0-9_]+$/i';

	/**
	 * @var IFormControl[]
	 */
	private $controls = array();

	/**
	 * Gets the instances of inner control
	 * @return IFormControl[]
	 */
	abstract protected function getControls();

	function __construct($name, $label)
	{
		Assert::isTrue(preg_match(self::NAME_PATTERN, $name));

		parent::__construct($name, $label);

		// initialize correct internals
		$this->setDefaultValue(array());
	}

	/**
	 * Set is always optional as can consist of 0 elements by design.
	 * @return bool
	 */
	final function isOptional()
	{
		return true;
	}

	/**
	 * Gets the name for inner controls
	 * @return string
	 */
	function getInnerName()
	{
		return $this->getName() . '[]';
	}

	function getIterator()
	{
		return new ArrayIterator($this->getControls());
	}

	function count()
	{
		return sizeof($this->getControls());
	}

	function importValue($value)
	{
		if ($value && !is_array($value)) {
			$this->setError(FormControlError::invalid());
			$value = array();
		}
		else if (!$value) {
			$value = array();
		}

		$this->setImportedValue($value);

		return !$this->hasError();
	}

	function setDefaultValue($value)
	{
		if (!is_array($value)) {
			$value =
				$value
					? array($value)
					: array();
		}

		return parent::setDefaultValue($value);
	}

	function toHtml(array $htmlAttributes = array())
	{
		Assert::isUnreachable('Use foreach(%s as $control) instead', get_class($this));
	}

	protected function setError(FormControlError $error)
	{
		Assert::isFalse($error->is(FormControlError::MISSING), '%s cannot be missing', get_class($this));

		return parent::setError($error);
	}
}

?>