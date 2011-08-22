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
 * Represents a key=>value associative array of ISqlValueExpression.
 * @ingroup Dal_DB_Sql
 */
final class SqlRow implements ISqlCastable
{
	/**
	 * @var ISqlValueExpression
	 */
	private $values = array();

	function setValue($field, ISqlValueExpression $value)
	{
		Assert::isScalar($field);

		$this->values[$field] = $value;

		return $this;
	}

	function setValues(array $values)
	{
		foreach ($values as $field => $value) {
			$this->setValue($field, $value);
		}

		return $this;
	}

	function getFields()
	{
		return array_keys($this->values);
	}

	function getValues()
	{
		return $this->values;
	}

	function toDialectString(IDialect $dialect)
	{
		$fieldValueCompiledPairs = array();

		foreach ($this->values as $field => $value) {
			$fieldValueCompiledPairs[] =
				  $dialect->quoteIdentifier($field) . '='
				. $value->toDialectString($dialect);
		}

		$compiledCollection = join(', ', $fieldValueCompiledPairs);
		return $compiledCollection;
	}
}

?>