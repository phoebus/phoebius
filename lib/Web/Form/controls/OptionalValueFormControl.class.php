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
 * Represents a control that expects a fixed value or null from the outer world
 * @ingroup Form
 */
abstract class OptionalValueFormControl extends InputFormControl
{
	const ERROR_UNEXPECTED = 'unexpected control value';

	private $value;

	function __construct($name, $label, $value)
	{
		Assert::isScalar($value);

		$this->value = $value;

		parent::__construct($name, $label);
	}

	/**
	 * A fixed value we expect from outer world
	 * @return mixed
	 */
	final function getFixedValue()
	{
		return $this->value;
	}

	final function isOptional()
	{
		return true;
	}

	final function markRequired()
	{
		Assert::isUnreachable('nonsense');
	}

	final function markMissing($message = null)
	{
		Assert::isUnreachable('nonsense');
	}

	function setValue($value)
	{
		if ($value !== null && $value != $this->value) {
			$this->markWrong(self::ERROR_UNEXPECTED);
		}
		else {
			parent::setValue($value);
		}
	}

	function toHtml(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['name']));
		Assert::isFalse(isset($htmlAttributes['type']));
		Assert::isFalse(isset($htmlAttributes['value']));
		Assert::isFalse(isset($htmlAttributes['checked']));

		$htmlAttributes['name'] = $this->getName();
		$htmlAttributes['type'] = $this->getType();
		$htmlAttributes['value'] = $this->getFixedValue();
		if ($this->getValue())
			$htmlAttributes['checked'] = 'checked';

		return HtmlUtil::getNode('input', $htmlAttributes);
	}
}

?>