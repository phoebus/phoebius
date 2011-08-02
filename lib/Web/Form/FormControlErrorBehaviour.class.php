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
 * Defines the behaviour of control on import error (usually, FormControlError::WRONG for StringFormControl)
 * @ingroup Form
 */
final class FormControlErrorBehaviour extends Enumeration
{
	const LEAVE_AS_IS = 0;
	const SET_EMPTY = 1;
	const USE_DEFAULT = 2;

    static function leaveAsIs()
    {
        return new self (self::LEAVE_AS_IS);
    }

    static function setEmpty()
    {
        return new self (self::SET_EMPTY);
    }

    static function useDefault()
    {
        return new self (self::USE_DEFAULT);
    }
}

?>