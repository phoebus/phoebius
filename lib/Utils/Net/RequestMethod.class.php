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
 * Represents a HTTP request method
 *
 * @ingroup Utils_Net
 */
final class RequestMethod extends Enumeration
{
	const POST = 'POST';
	const GET = 'GET';
	const PUT = 'PUT';
	const HEAD = 'HEAD';
	
	/**
	 * @return RequestMethod
	 */
	static function get()
	{
		return new self (self::GET);
	}
	
	/**
	 * @return RequestMethod
	 */
	static function post()
	{
		return new self (self::POST);
	}
}

?>