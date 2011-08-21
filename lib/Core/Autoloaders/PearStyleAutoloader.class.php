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
 * Resolver for classes located on PEAR notation
 *
 * @ingroup Core_Bootstrap
 */
class PearStyleAutoloader extends IncludePathAutoloader
{
	function load($class)
	{
		if (strpos($class, "\0") !== false) {
			return;
		}

		$classpath = str_replace('_', '/', $class);

		parent::load($classpath);
	}
}

?>