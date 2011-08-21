<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2009 Scand Ltd.
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
 * Abstract class loader
 *
 * @ingroup Core_Bootstrap
 */
abstract class ClassLoader
{
	abstract function load($class);

	protected function loaded($class)
	{
		return class_exists($class, false) || interface_exists($class, false);
	}

	protected function fail($class)
	{
		if (class_exists('ClassNotFoundException', false))
			throw new ClassNotFoundException($class);
		else
			eval (
				'class ClassNotFoundException extends Exception {} '
				. ' throw new ClassNotFoundException ("' . $class . '");'
			);
	}
}