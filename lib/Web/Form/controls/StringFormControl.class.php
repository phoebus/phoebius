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
 * A string input
 * @ingroup Form
 */
class StringFormControl extends InputFormControl
{
	/**
	 * @return StringFormControl
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function getType()
	{
		return 'text';
	}
}

?>