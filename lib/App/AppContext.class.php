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
 * @ingroup App
 */
class AppContext implements IAppContext
{
	/**
	 * @var IServerState
	 */
	private $server;

	/**
	 * @var IAppRequest
	 */
	private $request;

	/**
	 * @var IAppResponse
	 */
	private $response;

	function __construct(IAppRequest $request, IAppResponse $response, IServerState $server)
	{
		$this->request = $request;
		$this->response = $response;
		$this->server = $server;
	}

	/**
	 * @return IServerState
	 */
	function getServer()
	{
		return $this->server;
	}

	/**
	 * @return IAppRequest
	 */
	function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return IAppResponse
	 */
	function getResponse()
	{
		return $this->response;
	}

	function __clone()
	{
		$this->request = clone $this->request;
		$this->respones = clone $this->response;
		$this->server = clone $this->server;
	}
}

?>