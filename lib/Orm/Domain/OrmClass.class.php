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
 * Represents an auxiliary representation or ORM-related entity internals.
 *
 * This is needed to hold the internal structures on entity notation entity but before the final
 * code is autogenerated
 *
 * @aux
 * @ingroup Orm_Domain
 */
class OrmClass implements IPhysicallySchematic, ILogicallySchematic, IQueryable
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array of OrmProperty
	 */
    private $properties = array();

	/**
	 * @var OrmProperty|null
	 */
	private $identifier;

	/**
	 * @var IEntityMapper
	 */
	private $map;

	/**
	 * @var IOrmEntityAccessor
	 */
	private $dao;

	/**
	 * @var boolean
	 */
	private $hasDao = true;

	/**
	 * @var string
	 */
	private $dbSchema;

	/**
	 * @var string
	 */
	private $dbTableName;

	function __sleep()
	{
		$vars = get_class_vars($this);

		unset ($vars['map']);
		unset ($vars['dao']);

		$serialize = array_keys($vars);

		return $vars;
	}

	/**
	 * Sets whether entity is DAO-related
	 *
	 * @param boolean $flag
	 *
	 * @return OrmClass itself
	 */
	function setHasDao($flag)
	{
		Assert::isBoolean($flag);

		$this->hasDao = $flag;

		return $this;
	}

	/**
	 * Determines whether entity is DAO-related
	 *
	 * @return boolean
	 */
	function hasDao()
	{
		return $this->hasDao;
	}

	/**
	 * Sets the name of the database schema (defined within DBPool) where an entity should be
	 * stored
	 *
	 * @param strign $dbSchema optional name of the database schema. NULL to set the default schema
	 *
	 * @return OrmClass itself
	 */
	function setDbSchema($dbSchema = null)
	{
		Assert::isScalarOrNull($dbSchema);

		$this->dbSchema = $dbSchema;

		return $this;
	}

	/**
	 * Sets the name of an entity. A name of a database table is generated automatically based
	 * on entity name, if a table name is not yet set.
	 * @param string $name
	 * @return OrmClass
	 */
	function setName($name)
	{
		Assert::isScalar($name);

		$this->name = $name;

		if (!$this->dbTableName) {
			$this->setDBTableName(
				strtolower(
					preg_replace(
						'/([a-z])([A-Z])/',
						'$1_$2',
						$this->name
					)
				)
			);
		}

		return $this;
	}

	/**
	 * Gets the name of an entity, if set
	 * @return string|null
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * Adds the new property to the ORM-related entity
	 *
	 * @throws OrmModelIntegrityException if the property with the same name already added
	 * @param OrmProperty $property
	 *
	 * @return OrmClass
	 */
	function addProperty(OrmProperty $property)
	{
		$name = $property->getName();

		if (isset($this->properties[$name])) {
			throw new OrmModelIntegrityException("Property {$property->getName()} already defined");
		}

		$this->properties[$name] = $property;

		return $this;
	}

	/**
	 * Get the names of all properties defined within an entity
	 *
	 * @return array of string
	 */
	function getPropertyNames()
	{
		return array_keys($this->properties);
	}

	/**
	 * Adds the identifier property  to the ORM-related entity
	 *
	 * @throws OrmModelIntegrityException if the property with the same name already added
	 * @param OrmProperty $property
	 *
	 * @return OrmClass
	 */
	function setIdentifier(OrmProperty $property)
	{
		$this->identifier = $property;

		$this->addProperty($property);

		return $this;
	}

	/**
	 * Determines whether ORM-related entity has the identifier
	 *
	 * @return boolean
	 */
	function hasIdentifier()
	{
		return !!$this->identifier;
	}

	/**
	 * Sets a custom name of a database table to use for storing the entity.
	 *
	 * If custom name is not set then it is autogenerated from the name of the entity
	 *
	 * @param string $dbTableName
	 *
	 * @return OrmClass itself
	 */
	function setDBTableName($dbTableName)
	{
		Assert::isScalar($dbTableName);

		$this->dbTableName = $dbTableName;

		return $this;
	}

	function getProperties()
	{
		return $this->properties;
	}

	function getIdentifier()
	{
		return $this->identifier;
	}

	function getLogicalSchema()
	{
		return $this;
	}

	function getPhysicalSchema()
	{
		return $this;
	}

	function getTable()
	{
		return $this->dbTableName;
	}

	function getEntityName()
	{
		return ucfirst($this->name);
	}

	function getFields()
	{
		$columns = array();

		foreach ($this->properties as $property) {
			foreach ($property->getFields() as $field) {
				$columns[] = $field;
			}
		}

		return $columns;
	}

	function getProperty($name)
	{
		if (!isset($this->properties[$name])) {
			throw new OrmModelIntegrityException("Property {$name} is not defined");
		}

		return $this->properties[$name];
	}

	function getNewEntity()
	{
		return new $this->name;
	}

	function getDao()
	{
		Assert::isTrue(
			$this->hasDao,
			'%s is dao-less entity',
			$this->name
		);

		if (!$this->dao) {
			$this->dao = new RdbmsDao(
				$this->dbSchema
					? DBPool::get($this->dbSchema)
					: DBPool::getDefault(),
				$this
			);
		}

		return $this->dao;
	}

	function getMap()
	{
		if (!$this->map) {
			$this->map = new OrmMap($this);
		}

		return $this->map;
	}
}

?>