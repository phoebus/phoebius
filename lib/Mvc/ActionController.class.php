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
 * @ingroup Mvc
 */
abstract class ActionController
{

	/**
	 * @var RouteData
	 */
	private $routeData;

	/**
	 * @var WebRequest
	 */
	private $request;

	function __construct(RouteData $routeData, WebRequest $webRequest)
	{
		$this->routeData = $routeData;
		$this->request = $webRequest;
	}

	/**
	 * @return WebRequest
	 */
	protected function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return RouteData
	 */
	protected function getRouteData()
	{
		return $this->routeData;
	}
}

?>