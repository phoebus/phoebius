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

// legacy check
require_once 'ClassLoader.class.php';

/**
 * Simple class loader that searches a class in a file with name <class>.<extension> within the
 * include_path.
 *
 * @ingroup Core_Bootstrap
 */
class IncludePathClassLoader extends ClassLoader
{
	private $extension = PHOEBIUS_CLASS_EXT;

	function setExtension($extension)
	{
		$this->extension = '.' . trim($extension, '.');

		return $this;
	}

	function getExtension()
	{
		return $this->extension;
	}

	function load($class)
	{
		if (strpos($class, "\0") !== false) {
			return;
		}

		$filename = $class . $this->extension;

		try {
			include_once $filename;
		}
		catch (Exception $e) {
			if (strstr($e->getMessage(), "Failed opening '{$filename}'"))
				$this->fail($class);
			else
				throw $e;
		}

		if (!$this->loaded($class))
			$this->fail($class);
	}
}