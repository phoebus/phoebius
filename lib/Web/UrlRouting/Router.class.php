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

/**
 * Simple LILO route chain implementation.
 *
 * Example:
 * @code
 * $router->any("/blog:controller/entry:action/?id");
 * @endcode
 *
 * @ingroup App_Web_Routing
 */
class Router implements IRouter
{
	private $routeData = array();
	private $routes = array();
	private $routeNames = array();
	
	function __construct(array $defaultRouteData = array())
	{
		$this->routeData = $defaultRouteData;
	}
	
	function process(WebRequest $request)
	{
		return $this->lookup($this->routes, $request);
	}
	
	/**
	 * @return Router
	 */
	function addRoute(Route $route)
	{
		if (($name = $route->getName())) {
			Assert::isFalse(isset($this->routeNames[$name]), 'route with name `%s` already defined', $name);
			
			$this->routeNames[$name] = $route;
		}
		
		$this->routes[] = $route;
		
		return $this;
	}
	
	/**
	 * @return Router
	 */
	function addRoutes(array $routes)
	{
		foreach ($routes as $route)
			$this->addRoute($route);
		
		return $this;
	}
	
	private function lookup(array $routes, WebRequest $request)
	{
		foreach ($routes as $route) {
			$result = $route->match($request);

			if (is_array($result)) {
				return
					new RouteData(
						$route, $this,
						array_replace(
							$this->routeData, 
							$result
						)
					);
			}
		}
		
		if (!empty($this->routeData)) {
			return new RouteData(new Route(), $this, $this->routeData);
		}
		
		throw new RouteException($request->getHttpUrl());
	}
}
