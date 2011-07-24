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

class CallbackFormControlConstraint implements IFormControlConstraint
{
	private $id, $message, $callback, $resetImportedValue;

	function __construct($id, $message, $callback, $resetImportedValue)
	{
		Assert::isScalar($id);
		Assert::isScalar($message);
		Assert::isCallback($callback);
		Assert::isBoolean($resetImportedValue);
		
		$this->id = $id;
		$this->message = $message;
		$this->callback = $callback;
		$this->resetImportedValue = $resetImportedValue;
	}

	function getId()
	{
		return $this->id;
	}

	function getMessage()
	{
		return $this->message;
	}

	function check($value)
	{
		return call_user_func($this->callback, $value);
	}

	function isRejectsImportedValue()
	{
		return $this->resetImportedValue;
	}
}

?>