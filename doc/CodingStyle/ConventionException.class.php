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
 * Thrown when a code does not follow the guideline
 * @ingroup CodingStyle
 */
class ConventionException extends Exception
{
	/**
	 * @var ConventionChapter
	 */
	private $chapter;

	function __construct(ConventionChapter $occuredErrorChapter)
	{
		$this->chapter = $occuredErrorChapter;
	}

	/**
	 * @return ConventionChapter
	 */
	function getChapter()
	{
		return $this->chapter;
	}
}

?>