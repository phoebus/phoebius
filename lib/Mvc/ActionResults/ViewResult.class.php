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
 * Represents HTML markup and raw PHP code, handled by UITemplateControl.
 *
 * @ingroup Mvc_ActionResults
 */
class ViewResult implements IActionResult
{
	/**
	 * @var View
	 */
	private $view;

	/**
	 * @var array
	 */
	private $data = array();

	/**
	 * @var string
	 */
	private $contentType = 'text/html';

	/**
	 * @var string
	 */
	private $charset = 'UTF-8';

	private $statusCode;

	function __construct(View $view, array $data = array())
	{
		$this->view = $view;
		$this->data = $data;
	}

	function setStatusCode(HttpStatus $statusCode)
	{
		$this->statusCode = $statusCode;

		return $this;
	}

	function setContentType($contentType)
	{
		Assert::isScalar($contentType);

		$this->contentType = $contentType;

		return $this;
	}

	function setCharset($charset)
	{
		Assert::isScalar($charset);

		$this->charset = $charset;

		return $this;
	}

	function handle(WebResponse $response)
	{
		$result = $this->view->render($this->data);

		// html obfuscation
		//$result = preg_replace('{>\s+<}s', '><', $result);

		if ($this->statusCode)
			$response->setStatus($this->statusCode);

		$response
			->addHeader('Content-Type', $this->contentType . ';charset=' . $this->charset)
			->write($result)
			->finish();
	}

}

?>