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
 * Implements a composite type handler
 *
 * @ingroup Orm_Types
 */
class CompositePropertyType extends OrmPropertyType
{
	/**
	 * @var IMappable
	 */
	private $entity;

	private $entityClass;

	// outer field name => type
	private $sqlTypes = array();

	function __construct(IMappable $entity)
	{
		$this->entity = $entity;
		$this->entityClass = $this->entity->getLogicalSchema()->getEntityName();

		foreach ($this->entity->getLogicalSchema()->getProperties() as $property) {
			$fields = array_combine(
				$property->getFields(),
				$property->getType()->getSqlTypes()
			);

			$this->sqlTypes = array_merge($this->sqlTypes, $fields);
		}
	}

	function getImplClass()
	{
		return $this->entityClass;
	}

	function assemble(array $tuple, FetchStrategy $fetchStrategy)
	{
		return $this->entity->getMap()->assemble(
			$this->entity->getLogicalSchema()->getNewEntity(),
			$tuple,
			$fetchStrategy
		);
	}

	function disassemble($value)
	{
		return $this->entity->getMap()->disassemble($value);
	}

	function getSqlTypes()
	{
		return $this->sqlTypes;
	}

	function getColumnCount()
	{
		return count($this->nameMappings);
	}

	protected function getCtorArgumentsPhpCode()
	{
		return array(
			$this->entityClass . '::orm()'
		);
	}
}

?>