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
	 * whether value is optional
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
	 * gets the value, either imported or default (might be null)
	 * @return mixed
	 */
	function getValue();

	/**
	 * Sets the default value
	 * @return IFormControl
	 */
	function setDefaultValue($value);

	/**
	 * Gets the default value, if any
	 * @return mixed|null
	 */
	function getDefaultValue();

	/**
	 * determines whether errors occured during value import
	 * @return boolean
	 */
	function hasError();

	/**
	 * determines whether value was missing
	 * @return string|boolean string or true on error, otherwise false/null
	 */
	function isMissing();

	/**
	 * whether import failed due errors. May return string (a describtion of an error)
	 * @return string|boolean string or true on error, otherwise false/null
	 */
	function isWrong();

	/**
	 * Resets the control
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