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
	function __construct($id, $label, $defaultInputCount = 1, $skipWrong = true)
	{
		parent::__construct($id, $label, array_fill(0, $defaultInputCount, null), $skipWrong);
	}

	protected function spawnSingle()
	{
		$control = new FileFormControl($this->getInnerId(), $this->getLabel());
		foreach ($this->getConstraints() as $constraint) {
			$control->addConstraint($constraint);
		}
		
		return $control;
	}
}

?>