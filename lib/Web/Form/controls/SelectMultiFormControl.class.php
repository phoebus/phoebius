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
 * Multi-select drop down menu
 * @ingroup Form
 */
final class SelectMultiFormControl extends SetFormControl
{
	function setDefaultValue($value)
	{
		Assert::isTrue(is_array($value));
		Assert::isTrue(is_array($value));
		Assert::isTrue(
			sizeof($value)
			== array_intersect($this->getAvailableValues(), $value)
		);

		return parent::setDefaultValue($value);
	}

	function importValue($value)
	{
		if (!$value && !$this->isOptional()) {
			$this->markMissing();
			return false;
		}

		if ($value && !is_array($value)) {
			$this->markWrong();
			return false;
		}

		if (sizeof($value) != array_intersect($this->getAvailableValues(), $value)) {
			$this->markWrong();
			return false;
		}

		return parent::importValue($value);
	}

	function toHtml(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['name']));
		Assert::isFalse(isset($htmlAttributes['multiple']));

		$htmlAttributes['name'] = $this->getName();
		$htmlAttributes['multiple'] = $this->getName();

		return HtmlUtil::getContainer('select', $htmlAttributes, join("", $this->getOptions()));
	}
}

?>