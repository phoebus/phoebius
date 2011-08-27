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
 * Application infrastructure initializer.
 *
 * Grabs the standart incoming request, wraps it with appropriate objects, and handles the request
 * by passing those objects to the corresponding route.
 *
 * Consider index.php example:
 * @code
 * $app = new SiteApplication(new ChainedRouter);
 * $app->run();
 * @endcode
 *
 * The best practise is to implement your own SiteApplication class by extending this one
 *
 * @ingroup App_Web
 */
class WebApplication extends Application
{
	private $router;

}

?>