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
 * Represents a file control
 * @ingroup File
 */
class FileFormControl extends InputFormControl
{
	// missing
	const ERROR_NOTHING = 'nothing was uploaded'; // UPLOAD_ERR_NO_FILE

	// wrong
	const ERROR_SIZE = 'file exceeds the allowed size'; //UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE,
	const ERROR_PARTIAL = 'file was partially uploaded'; // UPLOAD_ERR_PARTIAL
	const ERROR_DENIED = 'file of that type is not allowed'; // UPLOAD_ERR_EXTENSION

	// asserts: UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE

	private $filepath;
	private $maxSize;
	private $noEmpty = true;

	function setMaxSize($bytes)
	{
		Assert::isPositiveInteger($bytes);

		$this->maxSize = $bytes;

		return $this;
	}

	function setNoEmptyFile($flag = true)
	{
		Assert::isBoolean($flag);

		$this->noEmpty = $flag;

		return $this;
	}

	function move($filepath)
	{
		return move_uploaded_file($this->getImportedFilepath(), $filepath);
	}

	function getImportedName()
	{
		$value = $this->getValue();
		if ($value) {
			return $value['name'];
		}
	}

	function getImportedFilepath()
	{
		$value = $this->getValue();
		if ($value) {
			return $value['tmp_name'];
		}
	}

	function getImportedSize()
	{
		$value = $this->getValue();
		if ($value) {
			return $value['size'];
		}
	}

	function getType()
	{
		return 'file';
	}

	final function setDefaultValue($value)
	{
		// do nothing
	}

	function importValue($value)
	{
		if (
				!$value
				|| !is_array($value)
				|| !isset($value['name'])
				|| !isset($value['tmp_name'])
				|| !isset($value['error'])
				|| !isset($value['size'])
		) {
			$this->markMissing(self::ERROR_NOTHING);
			return false;
		}
		else if ($value['error']) {
			switch ($value['error']) {

				case UPLOAD_ERR_NO_FILE: {
					$this->markMissing(self::ERROR_NOTHING);
					break;
				}

				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE: {
					$this->markWrong(self::ERROR_SIZE);
					break;
				}

				case UPLOAD_ERR_PARTIAL: {
					$this->markWrong(self::ERROR_PARTIAL);
					break;
				}

				case UPLOAD_ERR_EXTENSION: {
					$this->markWrong(self::ERROR_DENIED);
					break;
				}

				case UPLOAD_ERR_NO_TMP_DIR:
				case UPLOAD_ERR_CANT_WRITE: {
					Assert::isUnreachable('Unable to save tmp file');
					break;
				}
			}

			return false;
		}
		else if (!is_uploaded_file($value['tmp_name'])) {
			$this->markMissing(self::ERROR_NOTHING);
			return false;
		}

		// perform extra checks
		if ($this->noEmpty && !$value['size']) {
			$this->markMissing(self::ERROR_NOTHING);
			return false;
		}

		if ($this->maxSize && $this->maxSize < $value['size']) {
			$this->markWrong(self::ERROR_SIZE);
			return false;
		}

		return parent::importValue($value);
	}

	function __destruct()
	{
		$value = $this->getValue();
		if ($value) {
			try {
				unlink($value);
			}
			catch (ExecutionContextException $e) {}
		}
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