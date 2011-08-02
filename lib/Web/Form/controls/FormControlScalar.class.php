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
	function importValue($value)
	{
		if (!$value && !$this->isOptional()) {
			$this->setError(FormControlError::missing());
		}
		else if ($value && !is_scalar($value)) {
			$this->setError(FormControlError::invalid());
		}

		$this->setImportedValue($value);

		return !$this->hasError();
	}

	function setDefaultValue($value)
	{
		Assert::isScalarOrNull($value);

		return parent::setDefaultValue($value);
	}
}

?>