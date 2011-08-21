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
 * Implements a class resolving mechanism used to search files containing the requested classes
 * @ingroup Core_Bootstrap
 */
final class IncludePathAutoloader
{
	private $extension = PHOEBIUS_CLASS_EXT;

	function create()
	{
		return new self;
	}

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

		try {
			include $class . $this->extension;
		}
		catch (Exception $e) {
			$message = sprintf(
				'Exception thrown when autoloading %s from %s:%s: %s',
				$class, $e->getFile(), $e->getLine(), $e->getMessage()
			);

			throw new Exception($message);
		}
	}

	function register()
	{
		spl_autoload_register(array($this, 'load'));

		return $this;
	}

	function unregister()
	{
		spl_autoload_unregister(array($this, 'load'));

		return $this;
	}
}