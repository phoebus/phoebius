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
	/**
	 * @return SelectFormControl
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function setDefaultValue($value)
	{
		Assert::isTrue(
			!$value || in_array($value, $this->getAvailableValues()),
			'trying to set a default value that is out of options range'
		);

		return parent::setDefaultValue($value);
	}

	function importValue($value)
	{
		if (!in_array($value, $this->getAvailableValues())) {
			$value = null;
			$this->setError(FormControlError::invalid());
		}

		if (!$value && !$this->isOptional()) {
			$this->setError(FormControlError::missing());
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