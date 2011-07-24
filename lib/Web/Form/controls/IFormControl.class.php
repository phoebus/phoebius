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
	 * gets the id of a control
	 * @return string
	 */
	function getId();
	
	/**
	 * gets the control label
	 * @return string
	 */
	function getLabel();
	
	/**
	 * gets the value, either imported or default (might be null)
	 * @return mixed
	 */
	function getValue();
	
	/**
	 * tries to import the value
	 * @param mixed $value
	 * @return boolean wheter import was successful or not (caused import errors)
	 */
	function importValue($value);
	
	/**
	 * @return mixed
	 */
	function exportValue();
	
	/**
	 * determines whether errors occured during value import
	 * @return boolean
	 */
	function hasErrors();
	
	/**
	 * determines whether an error identified by $id occured during import
	 * @param string $id
	 * @return boolean
	 */
	function hasError($id);
	
	/**
	 * gets the hash of errors occured during import (might be empty if no errors occured during
	 * import)
	 * @return array
	 */
	function getErrors();
	
	/**
	 * gets the HTML representation of a form
	 * @param array $htmlAttributes
	 * @return string
	 */
	function toHtml(array $htmlAttributes = array());
}

?>