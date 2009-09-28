<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

/**
 * Stores the information about phusical structure of a property
 * @ingroup OrmModel
 */
interface IPropertyStructurized
{
	/**
	 * Returns an array (or an associative array) of {@link DBType} for the property
	 * @return array of {@link DBType}
	 */
	function getDbColumns();
}

?>