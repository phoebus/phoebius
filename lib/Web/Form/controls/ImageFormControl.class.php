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
final class ImageFormControl extends FileFormControl
{
	private $allowedImageTypes = array(
		IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_JPEG2000, IMAGETYPE_PNG
	);

	/**
	 * @return ImageFormControl
	 */
	static function create($name, $label)
	{
		return new self ($name, $label);
	}

	function addAllowedType($type)
	{
		$this->allowedImageTypes[] = $type;

		return $this;
	}

	function addAllowedTypes(array $types)
	{
		foreach ($types as $type) {
			$this->addAllowedType($type);
		}

		return $this;
	}

	function setAllowedTypes(array $types)
	{
		$this
				->dropAllowedTypes()
				->addAllowedTypes($types);

		return $this;
	}

	function dropAllowedTypes()
	{
		$this->allowedImageTypes = array();

		return $this;
	}

	function getAllowedTypes()
	{
		return $this->allowedImageTypes;
	}

	function hasAllowedType($type)
	{
		return in_array($type, $this->allowedImageTypes);
	}

	protected function setImportedValue($value)
	{
		if ($value) {
			try {
				$size = getimagesize($value['tmp_name']);
				if (
						$size
						&& $size[0]/*width*/
						&& $size[1]/*height*/
						&& in_array($size[2], $this->allowedImageTypes)
					) {
					parent::setImportedValue($value);
				}
				else {
					throw new Exception;
				};
			}
			catch (Exception $e) {
				$this->setError(FormControlError::wrong()->setMessage(self::ERROR_WRONG_FILE));
			}
		}
	}
}

?>