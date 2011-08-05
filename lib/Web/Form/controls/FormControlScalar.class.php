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
 * Represents a control that expects scalar value from the outer world
 * @ingroup Form
 */
abstract class FormControlScalar extends BaseFormControl
{
	/**
	 * @var bool
	 */
	private $isOptional;

	function importValue($value)
	{
		if ($value && !is_scalar($value)) {
			$value = null;
			$this->setError(FormControlError::invalid());
		}
		else if (!$value && !$this->isOptional()) {
			$this->setError(FormControlError::missing());
		}

		$this->setImportedValue($value);

		return !$this->hasError();
	}

	function setDefaultValue($value)
	{
		Assert::isScalarOrNull($value);

		return parent::setDefaultValue($value);
	}

	function isOptional()
	{
		return $this->isOptional;
	}

	/**
	 * Marks control as optional
	 * @return BaseFormControl
	 */
	function markOptional()
	{
		$this->isOptional = true;

		return $this;
	}

	/**
	 * Marks control as required
	 * @return BaseFormControl
	 */
	function markRequired()
	{
		$this->isOptional = false;

		return $this;
	}
}

?>