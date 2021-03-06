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
 * Represents a hidden value
 * @ingroup Form
 */
class HiddenFormControl extends InputFormControl
{
	/**
	 * @return HiddenFormControl
	 */
	static function create($name)
	{
		return new self ($name);
	}

	function __construct($name)
	{
		parent::__construct($name, 'hidden value ' . $name);
	}

	function getType()
	{
		return 'hidden';
	}
}

?>