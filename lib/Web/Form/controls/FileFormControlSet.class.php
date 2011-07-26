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

class FileFormControlSet extends FormControlSet
{
	function __construct($name, $label, $defaultInputCount = 1)
	{
		parent::__construct($name, $label);

		$this->setDefaultValue(array_fill(0, $defaultInputCount, $this->getLabel()));
	}

	protected function spawnSingle()
	{
		return new FileFormControl($this->getInnerName(), $this->getLabel());
	}
}

?>