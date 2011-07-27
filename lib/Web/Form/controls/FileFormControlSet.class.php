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
 * File set
 * @ingroup File
 */
class FileFormControlSet extends FormControlSet
{
	/**
	 * @return FileFormControlSet
	 */
	static function create($name, $label, $defaultInputCount = 1)
	{
		return new self ($name, $label, $defaultInputCount);
	}

	function __construct($name, $label, $defaultInputCount = 1)
	{
		parent::__construct($name, $label);

		$this->setDefaultValue(array_fill(0, $defaultInputCount, $this->getLabel()));
	}

	function importValue($value)
	{
		if (
				$value
				&& is_array($value)
				&& isset($value['name'])
				&& isset($value['tmp_name'])
				&& isset($value['error'])
				&& isset($value['size'])
		) {
			$fixed = array();
			foreach ($value['tmp_name'] as $idx => $_) {
				$fixed[$idx] = array(
					'name'     => $value['name'][$idx],
					'tmp_name' => $value['tmp_name'][$idx],
					'size'     => $value['size'][$idx],
					'error'    => $value['error'][$idx],
					'type'     => (isset($value['type'][$idx])? $value['type'][$idx]: null),
				);
			}

			$value = $fixed;
		}

		return parent::importValue($value);
	}

	protected function spawnSingle()
	{
		return new FileFormControl($this->getInnerName(), $this->getLabel());
	}
}

?>