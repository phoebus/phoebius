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
	private $defaultValue;

	/**
	 * @return SelectMultiFormControl
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function setDefaultValue($value)
	{
		if ($value) {
			if (!is_array($value))
				$value = array($value);

			Assert::isTrue(
				sizeof($value)
				== sizeof(array_intersect($this->getAvailableValues(), $value))
			);
		}

		$this->defaultValue = $value;

		return $this;
	}

	function getDefaultValue()
	{
		return $this->defaultValue;
	}

	function getSelectedValues()
	{
		$value = $this->getValue();
		return
			$value
				? $value
				: array();
	}

	function importValue($value)
	{
		if ($value && !is_array($value)) {
			$this->setError(FormControlError::invalid());
			return false;
		}

		$value = array_intersect($this->getAvailableValues(), $value);

		if (!$value && !$this->isOptional()) {
			$this->setError(FormControlError::missing());
			return false;
		}


		$this->setImportedValue($value);

		return true;
	}

	function toHtml(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['name']));
		Assert::isFalse(isset($htmlAttributes['multiple']));

		$htmlAttributes['name'] = $this->getName();
		$htmlAttributes['multiple'] = 'multiple';

		return HtmlUtil::getContainer('select', $htmlAttributes, join("", $this->getOptions()));
	}
}

?>