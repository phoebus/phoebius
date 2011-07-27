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
 * @ingroup Form
 */
class FormControlError extends Enumeration
{
	const MISSING = 1;
	const WRONG = 2;

	static function missing()
	{
		return new self (self::MISSING);
	}

	static function wrong()
	{
		return new self (self::WRONG);
	}
}

?>