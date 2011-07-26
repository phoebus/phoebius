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
 * A select drop-down menu
 * @ingroup Form
 */
final class SelectFormControl extends SetFormControl
{
	function setDefaultValue($value)
	{
		Assert::isScalarOrNull($value);
		Assert::isTrue(in_array($value, $this->getAvailableValues()));

		return parent::setDefaultValue($value);
	}

	function importValue($value)
	{
		if (!$value && !$this->isOptional()) {
			$this->markMissing();
			return false;
		}

		if ($value && !is_scalar($value)) {
			$this->markWrong();
			return false;
		}

		if (!in_array($value, $this->getAvailableValues())) {
			$this->markWrong();
			return false;
		}

		return parent::importValue($value);
	}

	function toHtml(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['name']));

		$htmlAttributes['name'] = $this->getName();

		return HtmlUtil::getContainer('select', $htmlAttributes, join("", $this->getOptions()));
	}
}

?>