<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * An abstract representation of SQL-castable value
 * @ingroup Dal_DB_Sql
 */
class SqlValue implements ISqlValueExpression
{
	/**
	 * @var mixed|null
	 */
	private $value;

	function __construct($value)
	{
		$this->setValue($value);
	}

	function getValue()
	{
		return $this->value;
	}

	function setValue($value = null)
	{
		$this->value = $value;
	}

	function toDialectString(IDialect $dialect)
	{
		return $dialect->quoteValue($this->value);
	}
}

?>