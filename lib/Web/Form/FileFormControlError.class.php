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
final class FileFormControlError extends FormControlError
{
	const MAX_SIZE_EXCEEDED = 'file exceeds the allowed size'; //UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE,
	const TOO_SMALL = 'file is empty';
	const UPLOAD_IS_PARTIAL = 'file was partially uploaded'; // UPLOAD_ERR_PARTIAL
	const DISALLOWED_FILE_TYPE = 'file of that type is not allowed'; // UPLOAD_ERR_EXTENSION

	// asserts: UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE

	private static $messages = array(
		self::MAX_SIZE_EXCEEDED => 'file exceeds the allowed size',
		self::TOO_SMALL => 'file size is too small',
		self::UPLOAD_IS_PARTIAL => 'file was partially uploaded',
		self::DISALLOWED_FILE_TYPE => 'file of that type is not allowed'
	);

	/**
	 * @return string
	 */
	function getMessage()
	{
		$m = parent::getMessage();

		if (!$m) {
			$m = self::$messages[$this->getValue()];
		}

		return $m;
	}
}

?>