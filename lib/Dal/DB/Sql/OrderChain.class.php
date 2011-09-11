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
 * Represents a chain of expressions used in ordering the resulting database rows
 *
 * @ingroup Dal_DB_Sql
 */
class OrderChain implements ISubjective, ISqlCastable
{
	/**
	 * @var SqlValueExpressionList
	 */
	private $chain;

	function __construct()
	{
		$this->chain = new SqlValueExpressionList;
	}

	function add(OrderBy $orderBy)
	{
		$this->chain->add($orderBy);

		return $this;
	}

	function getList()
	{
		return $this->chain->getList();
	}

	function isEmpty()
	{
		return $this->chain->isEmpty();
	}

	function toSubjected(ISubjectivity $object)
	{
		$me = new self;

		foreach ($this->chain->getList() as $elt) {
			$me->add($elt->toSubjected($object));
		}

		return $me;
	}

	function toDialectString(IDialect $dialect)
	{
		if (!$this->isEmpty()) {
			return 'ORDER BY ' . $this->chain->toDialectString($dialect);
		}
	}
}

?>