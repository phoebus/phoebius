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
 * Image set
 *
 * @ingroup File
 */
class ImageFormControlSet extends FileFormControlSet
{
	private $allowedImageTypes = array(
		IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_JPEG2000, IMAGETYPE_PNG
	);

	/**
	 * @return ImageFormControlSet
	 */
	static function create($name, $label, $defaultInputCount = 1)
	{
		return new self ($name, $label, $defaultInputCount);
	}

	function __construct($name, $label, $defaultInputCount = 1)
	{
		Assert::isPositiveInteger($defaultInputCount);

		parent::__construct($name, $label);

		$this->setDefaultValue(array_fill(0, $defaultInputCount, null));
	}

	function addAllowedType($type)
	{
		$this->allowedImageTypes[] = $type;

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

	protected function spawnControl()
	{
		return ImageFormControl::create($this->getInnerName(), $this->getLabel())
				->addAllowedTypes($this->allowedImageTypes);
	}
}

?>