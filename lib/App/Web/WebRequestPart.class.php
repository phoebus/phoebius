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
 * @ingroup App_Web
 */
class WebRequestPart extends Enumeration
{
	const GET = 'GET';
	const POST = 'POST';
	const FILES	= 'FILES';
	const COOKIE = 'COOKIE';
//	const SESSION = 'SESSION';
//	const ATTACHED;
//	const SERVER = 'SERVER';

	/**
	 * @return WebRequestPart
	 */
	static function get()
	{
		return new self (self::GET);
	}

	/**
	 * @return WebRequestPart
	 */
	static function post()
	{
		return new self (self::POST);
	}

	/**
	 * @return WebRequestPart
	 */
	static function files()
	{
		return new self (self::FILES);
	}

	/**
	 * @return WebRequestPart
	 */
	static function cookie()
	{
		return new self (self::COOKIE);
	}
}

?>