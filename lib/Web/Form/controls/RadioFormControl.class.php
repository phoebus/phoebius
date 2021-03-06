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
 * Radio button
 * @ingroup Form
 */
class RadioFormControl extends OptionalValueFormControl
{
	/**
	 * @return RadioFormControl
	 */
	static function create($name, $label, $value)
	{
		return new self ($name, $label, $value);
	}

	function getType()
	{
		return 'radio';
	}
}

?>