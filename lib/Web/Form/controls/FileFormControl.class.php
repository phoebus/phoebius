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
	const ERROR_MISSING_FILE = 'nothing was uploaded'; // UPLOAD_ERR_NO_FILE

	// wrong
	const ERROR_WRONG_MAX_SIZE = 'file exceeds the allowed size'; //UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE,
	const ERROR_WRONG_MIN_SIZE = 'file is empty';
	const ERROR_WRONG_UPLOAD_IS_PARTIAL = 'file was partially uploaded'; // UPLOAD_ERR_PARTIAL
	const ERROR_WRONG_FILE = 'file of that type is not allowed'; // UPLOAD_ERR_EXTENSION

	// asserts: UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE

	/**
	 * @var int
	 */
	private $maxSize;

	/**
	 * @var int
	 */
	private $minSize;

	/**
	 * @return FileFormControl
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function setMaxSize($bytes)
	{
		Assert::isPositiveInteger($bytes);

		$this->maxSize = $bytes;

		return $this;
	}

	function setMinSize($bytes)
	{
		Assert::isPositiveInteger($bytes);

		$this->minSize = $bytes;

		return $this;
	}

	function move($filepath)
	{
		Assert::isScalar($filepath);
		Assert::isTrue(is_dir(dirname($filepath)));
		Assert::isTrue(is_writable(dirname($filepath)));

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
			$value = null;
			if (!$this->isOptional())
				$this->setError(FormControlError::missing()->setMessage(self::ERROR_MISSING_FILE));
		}
		else if ($value['error']) {
			switch ($value['error']) {
				case UPLOAD_ERR_NO_FILE: {
					if (!$this->isOptional())
						$this->setError(FormControlError::missing()->setMessage(self::ERROR_MISSING_FILE));
					break;
				}

				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE: {
					$this->setError(FormControlError::wrong()->setMessage(self::ERROR_WRONG_MAX_SIZE));
					break;
				}

				case UPLOAD_ERR_PARTIAL: {
					$this->setError(FormControlError::wrong()->setMessage(self::ERROR_WRONG_UPLOAD_IS_PARTIAL));
					break;
				}

				case UPLOAD_ERR_EXTENSION: {
					$this->setError(FormControlError::wrong()->setMessage(self::ERROR_WRONG_FILE));
					break;
				}

				case UPLOAD_ERR_NO_TMP_DIR:
				case UPLOAD_ERR_CANT_WRITE: {
					Assert::isUnreachable('Unable to save tmp file (error %s)', $value['error']);
					break;
				}
			}

			$value = null;
		}
		else if (!is_uploaded_file($value['tmp_name'])) {
			$value = null;
			if (!$this->isOptional())
				$this->setError(FormControlError::missing()->setMessage(self::ERROR_MISSING_FILE));
		}
		else if ($this->minSize && $value['size'] < $this->minSize) {
			$this->setError(FormControlError::wrong()->setMessage(self::ERROR_WRONG_MIN_SIZE));
		}
		else if ($this->maxSize && $this->maxSize < $value['size']) {
			$this->setError(FormControlError::wrong()->setMessage(self::ERROR_WRONG_MAX_SIZE));
		}

		$this->setImportedValue($value);

		return !$this->hasError();
	}

	function __destruct()
	{
		if ($this->isImported() && !$this->hasError()) {
			try {
				unlink($this->getImportedFilepath());
			}
			catch (ErrorException $e) {}
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