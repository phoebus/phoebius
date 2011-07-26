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

class FileFormControl extends InputFormControl
{
	const ERROR_UPLOAD_FAILED = 'upload_failed';
	const ERROR_FILETYPE = 'filetype';
	const ERROR_FILESIZE = 'filesize';

	function getType()
	{
		return 'file';
	}

	function importValue($value)
	{
		Assert::notImplemented();

		if (!isset($value['tmp_name']) || !is_uploaded_file($value['tmp_name'])) {
			$this->addError(self::ERROR_UPLOAD_FAILED, 'file is not uploaded during network lags');
			return;
		}

		Assert::notImplemented();

		return parent::importValue($value);
	}

	function toHtml(array $htmlAttributes = array())
	{
		Assert::isFalse(isset($htmlAttributes['name']));
		Assert::isFalse(isset($htmlAttributes['type']));

		$htmlAttributes['name'] = $this->getName();
		$htmlAttributes['type'] = $this->getType();

		return HtmlUtil::getNode('input', $htmlAttributes);
	}
}

?>