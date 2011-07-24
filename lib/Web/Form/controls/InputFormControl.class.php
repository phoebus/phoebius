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

abstract class InputFormControl extends FormControlScalar 
{
	abstract function getType();
	
	function toHtml(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['name']));
		Assert::isFalse(isset($htmlAttributes['type']));
		Assert::isFalse(isset($htmlAttributes['value']));
		
		$htmlAttributes['name'] = $this->getId();
		$htmlAttributes['type'] = $this->getType();
		$htmlAttributes['value'] = $this->getValue();
		
		return HtmlUtil::getNode('input', $htmlAttributes);
	}
}

?>