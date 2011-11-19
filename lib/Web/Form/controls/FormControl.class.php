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

final class FormControl extends StaticClass
{
	/**
	 * @return ButtonFormControl
	 */
	static function button($name, $label)
	{
		return ButtonFormControl::create($name, $label);
	}

	/**
	 * @return CheckboxFormControl
	 */
	static function checkbox($name, $label, $checked = false, $value = '1')
	{
		return CheckboxFormControl::create($name, $label, $value)
					->setDefaultValue($checked ? $value : null);
	}

	/**
	 * @return CheckboxFormControlSet
	 */
	static function checkboxSet($name, $label, array $options)
	{
		return CheckboxFormControlSet::create($name, $label)
				->setOptions($options);
	}

	/**
	 * @return FileFormControl
	 */
	static function file($name, $label)
	{
		return FileFormControl::create($name, $label);
	}

	/**
	 * @return ImageFormControl
	 */
	static function image($name, $label)
	{
		return ImageFormControl::create($name, $label);
	}

	/**
	 * @return FileFormControlSet
	 */
	static function fileSet($name, $label, $defaultCount = 1)
	{
		return FileFormControlSet::create($name, $label, $defaultCount);
	}

	/**
	 * @return ImageFormControlSet
	 */
	static function imageSet($name, $label, $defaultCount = 1)
	{
		return ImageFormControlSet::create($name, $label, $defaultCount);
	}

	/**
	 * @return HiddenFormControl
	 */
	static function hidden($name, $value = null)
	{
		return HiddenFormControl::create($name)
				->setDefaultValue($value);
	}

	/**
	 * @return HiddenFormControlSet
	 */
	static function hiddenSet($name, $label, array $values = array())
	{
		return HiddenFormControlSet::create($name, $label)
				->setDefaultValue($values);
	}

	/**
	 * @return PasswordFormControl
	 */
	static function password($name, $label)
	{
		return PasswordFormControl::create($name, $label);
	}

	/**
	 * @return RadioFormControlSet
	 */
	static function radioGroup($name, $label, array $options)
	{
		return RadioFormControlSet::create($name, $label)
				->setOptions($options);
	}

	/**
	 * @return SelectFormControl
	 */
	static function select($name, $label, array $options)
	{
		return SelectFormControl::create($name, $label)
				->setLabels($options);
	}

	/**
	 * @return SelectMultiFormControl
	 */
	static function selectMulti($name, $label, array $options)
	{
		return SelectMultiFormControl::create($name, $label)
				->setLabels($options);
	}

	/**
	 * @return StringFormControl
	 */
	static function url($name, $label, $value = null)
	{
		return self::string($name, $label, $value)
				->setPattern(StringFormControl::URL_PATTERN);
	}

	/**
	 * @return StringFormControl
	 */
	static function email($name, $label, $value = null)
	{
		return self::string($name, $label, $value)
				->setPattern(StringFormControl::MAIL_PATTERN);
	}

	/**
	 * @return StringFormControl
	 */
	static function string($name, $label, $value = null)
	{
		return StringFormControl::create($name, $label)
				->setDefaultValue($value);
	}

	/**
	 * @return StringFormControlSet
	 */
	static function stringSet($name, $label, array $values = array())
	{
		return StringFormControlSet::create($name, $label)
				->setDefaultValue($values);
	}

	/**
	 * @return TextareaFormControl
	 */
	static function textarea($name, $label, $value = null)
	{
		return TextareaFormControl::create($name, $label)
				->setDefaultValue($value);
	}
}

?>