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
	const ERROR_PATTERN = 'value didn`t pass a pattern';

	const MAIL_PATTERN 	= '/^[a-zA-Z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~]+(\.[a-zA-Z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~]+)*@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/Ds';
	const URL_PATTERN 	= '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}((:[0-9]{1,5})?\/.*)?$/is';

	private $pattern;
	private $rejectWrong;

	/**
	 * @return StringFormControl
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function setPattern($pattern)
	{
		Assert::isScalar($pattern);

		$this->pattern = $pattern;

		return $this;
	}

	function getPattern()
	{
		return $this->pattern;
	}

	function rejectWrong($flag = true)
	{
		Assert::isBoolean($flag);

		$this->rejectWrong = $flag;

		return $this;
	}

	function isRejectsWrong()
	{
		return $this->rejectWrong;
	}

	function setValue($value)
	{
		if ($this->pattern) {
			if (!preg_match($this->pattern, $value)) {
				$this->markWrong(self::ERROR_PATTERN);
				if ($this->isRejectsWrong()) {
					$value = null;
				}
			}
		}

		parent::setValue($value);
	}

	function getType()
	{
		return 'text';
	}
}

?>