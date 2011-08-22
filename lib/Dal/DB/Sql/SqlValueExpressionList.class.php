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
 * Represents a list of ISqlValueExpression
 *
 * @see ISqlValueExpression
 * @ingroup Dal_DB_Sql
 */
class SqlValueExpressionList implements ISqlCastable
{
	/**
	 * @var ISqlValueExpression[]
	 */
	private $list = array();

	static function create()
	{
		return new self;
	}

	/**
	 * @return ISqlValueExpression
	 */
	function getFirst()
	{
		Assert::isNotEmpty($this->list);

		return reset($this->list);
	}

	/**
	 * @return ISqlValueExpression
	 */
	function getLast()
	{
		Assert::isNotEmpty($this->list);

		return end($this->list);
	}

	function add(ISqlValueExpression $expression)
	{
		$this->list[] = $expression;

		return $this;
	}

	function setList(array $list)
	{
		foreach ($list as $value) {
			$this->add($value);
		}

		return $this;
	}

	/**
	 * @return ISqlValueExpression[]
	 */
	function getList()
	{
		return $this->list;
	}

	function isEmpty()
	{
		return empty ($this->list);
	}

	function toDialectString(IDialect $dialect)
	{
		$compiledSlices = array();
		foreach ($this->getList() as $element) {
			$compiledSlices[] = $element->toDialectString($dialect);
		}

		$compiledString = join(', ', $compiledSlices);

		return $compiledString;
	}
}

?>