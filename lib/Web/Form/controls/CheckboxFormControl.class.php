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

class CheckboxFormControl extends OptionalValueFormControl
{
	function getType()
	{
		return 'checkbox';
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