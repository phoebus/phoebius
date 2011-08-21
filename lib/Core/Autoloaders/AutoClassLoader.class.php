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
 * Autoloader
 *
 * @ingroup Core_Bootstrap
 */
final class AutoClassLoader extends ClassLoader
{
	/**
	 * @var AutoClassLoader
	 */
	private static $instance;

	/**
	 * @var ClassLoader[]
	 */
	private $loaders = array();

	/**
	 * @static
	 * @return AutoClassLoader
	 */
	static function getInstance()
	{
		return self::$instance;
	}

	function addLoader(ClassLoader $loader)
	{
		$this->loaders[] = $loader;

		return $this;
	}

	function load($class)
	{
		if (strpos($class, "\0") !== false) {
			return;
		}

		foreach ($this->loaders as $loader) {
			try {
				$loader->load($class);

				if ($this->loaded($class));
					return true;
			}
			catch (ClassNotFoundException $e) {}
		}

		$this->fail($class);
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

	private function __construct()
	{
		// singleton
	}
}