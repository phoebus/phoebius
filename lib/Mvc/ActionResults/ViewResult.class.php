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
	 * @var UITemplateControl
	 */
	private $view;

	/**
	 * @var string
	 */
	private $contentType = 'text/html';

	/**
	 * @var string
	 */
	private $charset = 'UTF-8';

	function __construct(View $view)
	{
		$this->view = $view;
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

	function handleResult(WebResponse $response)
	{
		$result = $this->view->render();

		$response
			->addHeader('Content-Type', $this->contentType . ';charset=' . $this->charset)
			->write($result)
			->finish();
	}

}

?>