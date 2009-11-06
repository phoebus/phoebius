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
 * Represents a custom key=>value collection
 * @ingroup Core_Patterns
 */
class Collection implements IteratorAggregate, ArrayAccess
{
	/**
	 * Represents a key=>value collection
	 * @var array
	 */
	private $collection = array();

	/**
	 * @return boolean
	 */
	function offsetExists($offset)
	{
		return $this->has($offset);
	}

	/**
	 * @return mixed
	 */
	function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * @return void
	 */
	function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * @return void
	 */
	function offsetUnset($offset)
	{
		$this->drop($offset);
	}

	/**
	 * @return boolean
	 */
	function has($key)
	{
		return isset($this->collection[$key]);
	}

	/**
	 * @return mixed
	 */
	function get($key)
	{
		return $this->collection[$key];
	}

	/**
	 * @return Collection
	 */
	function set($key, $value)
	{
		$this->collection[$key] = $value;

		return $this;
	}

	/**
	 * @return Collection
	 */
	function drop($key)
	{
		unset($this->collection[$key]);

		return $this;
	}

	/**
	 * @return array
	 */
	function getKeys()
	{
		return array_keys($this->collection);
	}

	/**
	 * @return array
	 */
	function getValues()
	{
		return array_values($this->collection);
	}

	/**
	 * @see IteratorAggregate::getIterator()
	 * @return ArrayIterator
	 */
	function getIterator()
	{
		return new ArrayIterator($this->collection);
	}

	/**
	 * @return Collection
	 */
	function merge(Collection $collection)
	{
		$this->fill($collection->collection);

		return $this;
	}

	/**
	 * @return Collection
	 */
	function fill(array $values)
	{
		$this->collection = array_merge($this->collection, $values);

		return $this;
	}

	/**
	 * @return Collection
	 */
	function erase()
	{
		$this->collection = array();

		return $this;
	}

	/**
	 * @return array
	 */
	function toArray()
	{
		return $this->collection;
	}

	/**
	 * @return ArrayObject
	 */
	function toArrayObject()
	{
		return new ArrayObject($this->collection);
	}
}

?>