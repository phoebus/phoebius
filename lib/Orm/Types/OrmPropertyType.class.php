<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 phoebius.org
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
 * @ingroup Orm_Types
 */
abstract class OrmPropertyType
{
	/**
	 * @return string|null
	 */
	abstract function getImplClass();

	/**
	 * @return mixed native value
	 */
	abstract function assemble(DBValueArray $values, FetchStrategy $fetchStrategy);

	/**
	 * @return SqlValueArray
	 */
	abstract function disassemble($value);

	/**
	 * @return boolean
	 */
	abstract function isNullable();

	/**
	 * Returns an array of ISqlType for the property
	 * @return array of key=>ISqlType
	 */
	abstract function getSqlTypes();

	/**
	 * @return integer
	 */
	abstract function getColumnCount();

	/**
	 * @param array of DBValueArray
	 * @param FetchStrategy
	 * @return array of native values
	 */
	function assebmleSet(array $valueSet, FetchStrategy $fetchStrategy)
	{
		$yield = array();

		foreach ($valueSet as $values) {
			$yield = $this->assemble($values, $fetchStrategy);
		}

		return $yield;
	}

	/**
	 * @return string
	 */
	function toPhpCodeCall()
	{
		return join('', array(
			'new ',
			get_class($this),
			'(',
				join(', ', $this->getCtorArgumentsPhpCode()),
			')'
		));
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array();
	}
}

?>