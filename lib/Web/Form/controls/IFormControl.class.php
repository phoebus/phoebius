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
 * Rather tight form element interface
 *
 *  - selectmulti (scalar impl) - array
 *  - radio group (array impl) - scalar
 *
 *
 * @ingroup Form
 */
interface IFormControl
{
	/**
	 * gets the name of a control
	 * @return string
	 */
	function getName();

	/**
	 * gets the label of a control
	 * @return string
	 */
	function getLabel();

	/**
	 * whether control is optional
	 * @return boolean
	 */
	function isOptional();

	/**
	 * tries to import the value
	 * @param mixed $value
	 * @return boolean whether import was successful or not (caused import errors)
	 */
	function importValue($value);

	/**
	 * sets the default value
	 * @param mixed $value
	 * @return IFormControl
	 */
	function setDefaultValue($value);

	/**
	 * gets the value, either imported or default (might be null)
	 * @return mixed
	 */
	function getValue();

	/**
	 * determines whether errors occurred during value import
	 * @return boolean
	 */
	function hasError();

	/**
	 * @return FormControlError
	 */
	function getError();

	/**
	 * @return string
	 */
	function getErrorMessage();

	/**
	 * Resets the import state and errors
	 * @return IFormControl
	 */
	function reset();

	/**
	 * gets the HTML representation of a form
	 * @param array $htmlAttributes
	 * @return string
	 */
	function toHtml(array $htmlAttributes = array());
}

?>