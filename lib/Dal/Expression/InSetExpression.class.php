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
 * Represents the IN expression used in query logic
 *
 * SQL example:
 * @code
 * // "type" IN ("completed", "pending")
 * Expression::in("type", array("completed", "pending"));
 * @endcode
 *
 * @ingroup Dal_Expression
 */
class InSetExpression implements ISubjective, IExpression
{
	/**
	 * @var mixed
	 */
	private $subject;

	/**
	 * @var array
	 */
	private $set = array();

	/**
	 * @var InSetLogicalOperator
	 */
	private $operator;

	/**
	 * @param mixed $subject logical subject
	 * @param array $set set of values the subject should match
	 * @param InSetLogicalOperator|null $operator logical operator
	 */
	function __construct($subject, $set, InSetLogicalOperator $operator = null)
	{
		$this->subject = $subject;
		$this->set = $set;
		$this->operator =
			$operator
				? $operator
				: InSetLogicalOperator::in();
	}

	function toSubjected(ISubjectivity $object)
	{
		return new self (
			$object->subject($this->subject, $this),
			$this->convertSet($object),
			$this->operator
		);
	}

	/**
	 * @return array
	 */
	private function convertSet(ISubjectivity $object)
	{
		if (!is_array($this->set)) {
			return $object->subject($this->set);
		}
		else {
			$set = $this->set;

			$finalSet = array();
			foreach ($set as $item) {
				$finalSet[] = $object->subject($item);
			}
			return $finalSet;
		}
	}

	function toDialectString(IDialect $dialect)
	{
		if (empty($this->set)) {
			return null;
		}

		if (is_array($this->set))
			$this->set = SqlValueExpressionList::create()
							->setList($this->set);

		$values = $this->set->toDialectString($dialect);

		if (!$values)
			return null;

		$compiledSlices = array();

		$compiledSlices[] = '(';
		$compiledSlices[] = $this->subject->toDialectString($dialect);
		$compiledSlices[] = ')';
		$compiledSlices[] = $this->operator->toDialectString($dialect);
		$compiledSlices[] = '(';
		$compiledSlices[] = $values;
		$compiledSlices[] = ')';

		$compiledString = join(' ', $compiledSlices);

		return $compiledString;
	}
}

?>