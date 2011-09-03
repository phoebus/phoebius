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
	const MISSING = 'missing'; // nothing to import
	const INVALID = 'invalid'; // unexpected value type (won't be imported)
	const WRONG   = 'wrong';   // unable to import value

	/**
	 * @var FormControlErrorBehaviour
	 */
	private $behaviour;

	/**
	 * @var string
	 */
	private $message;

	static function missing()
	{
		return new self (self::MISSING);
	}

	static function invalid()
	{
		return new self (self::INVALID);
	}

	static function wrong()
	{
		return new self (self::WRONG);
	}

	/**
	 * @param FormControlErrorBehaviour $behavior
	 * @return FormControlError
	 */
	function setBehaviour(FormControlErrorBehaviour $behavior)
	{
		$this->behaviour = $behavior;

		return $this;
	}

	/**
	 * @return FormControlErrorBehaviour
	 */
	function getBehaviour()
	{
		if (!$this->behaviour) {
			if ($this->is(self::INVALID))
				$this->behaviour = FormControlErrorBehaviour::setEmpty();
			else
				$this->behaviour = FormControlErrorBehaviour::leaveAsIs();
		}

		return $this->behaviour;
	}

	/**
	 * @param $message
	 * @return FormControlError
	 */
	function setMessage($message)
	{
		Assert::isScalar($message);

		$this->message = $message;

		return $this;
	}

	/**
	 * @return string
	 */
	function getMessage()
	{
		return $this->message;
	}
}

?>