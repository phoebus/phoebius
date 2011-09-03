<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

class RouteData implements  ArrayAccess
{
	private $route;
	private $router;
	private $values = array();

	function __construct(Route $route, Router $router, array $values = array())
	{
		$this->routeObject = $route;
		$this->router = $router;
		$this->values = $values;
	}

	function __isset($name)
	{
		return isset($this->values[$name]);
	}

	function __get($name)
	{
		return $this->values[$name];
	}

	function __set($name, $value)
	{
		$this->values[$name] = $value;
	}

	function offsetExists($name)
	{
		return isset($this->values[$name]);
	}

	function offsetGet($name)
	{
		return $this->values[$name];
	}

	function offsetSet($name, $value)
	{
		$this->values[$name] = $value;
	}

	function offsetUnset($name)
	{
		unset($this->values[$name]);
	}

	function toArray()
	{
		return $this->values;
	}


	/**
	 * @return Route
	 */
	function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return Router
	 */
	function getRouter()
	{
		return $this->router;
	}
}
