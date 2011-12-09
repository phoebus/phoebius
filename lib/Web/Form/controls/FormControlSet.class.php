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
 * Represents a control set. foreach()-friendly.
 * @ingroup Form
 */
abstract class FormControlSet extends BaseFormControl implements IteratorAggregate, Countable
{
	const NAME_PATTERN = '/^[a-z0-9_-]+$/i';

	/**
	 * Gets the instances of inner control
	 * @return IFormControl[]
	 */
	abstract protected function getControls();

	function __construct($name, $label)
	{
		Assert::isTrue(
			preg_match(self::NAME_PATTERN, $name),
			'name of a set should contain alphanumeric symbols, glyphs and underscored only'
		);

		parent::__construct($name, $label);
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
			$value = array();
			$this->setError(FormControlError::invalid());
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
		$yield = '';
		foreach ($this->getControls() as $control) {
			$yield .= $control->toHtml($htmlAttributes);
		}

		return $yield;
	}

	function setError(FormControlError $error)
	{
		Assert::isFalse(
			$error->is(FormControlError::MISSING),
			'%s cannot be missing', get_class($this)
		);

		return parent::setError($error);
	}
}

?>