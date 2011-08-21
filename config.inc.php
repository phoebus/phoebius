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

//
// Overriddable constants:
//
// * PHOEBIUS_APPLICATION_ID - should be set in case when different application
//						use the same Phoebius framework
// * PHOEBIUS_TMP_ROOT - path to the tmp directory
//
// Constants to be set:
// * PHOEBIUS_APPLICATION_ENV
// * PHOEBIUS_APPLICATION_ROOT
//

date_default_timezone_set('Europe/London');
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

ob_start();
error_reporting(E_ALL);

// to catch fatal errors, register a shutdown function
// and check error_get_last() within it. On the other hand,
// do not even thing to throw exceptions out of it,
// you can only notify via mail or smth like that
set_error_handler('phoebius_error_catcher', E_ALL);
function phoebius_error_catcher()
{
	$error = error_get_last();

	// Handle error suppression with @ operator
	if (!$error || !error_reporting()) {
		return false;
	}

	throw new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
}

define('PHOEBIUS_VERSION', '2.0.0-dev');
define('PHOEBIUS_PRODUCT_NAME', 'Phoebius framework v'.PHOEBIUS_VERSION);

define('PHOEBIUS_CLASS_EXT', '.class.php');
define('PHOEBIUS_VIEW_EXT', '.view.php');
define('PHOEBIUS_CONFIG_PHP_EXT', '.config.php');
define('PHOEBIUS_CONFIG_YML_EXT', '.yml');
define('PHOEBIUS_CONFIG_XML_EXT', '.yml');

define('PHOEBIUS_BASE_ROOT', dirname(__FILE__));

if (!defined('PHOEBIUS_APPLICATION_ID')) {
	define('PHOEBIUS_APPLICATION_ID', 'default');
}

if (!defined('PHOEBIUS_TMP_ROOT')) {
	define(
		'PHOEBIUS_TMP_ROOT',
		sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Phoebius-v' . PHOEBIUS_VERSION . '-' . PHOEBIUS_APPLICATION_ID
	);
}

if (!is_dir(PHOEBIUS_TMP_ROOT)) {
	try {
		mkdir(PHOEBIUS_TMP_ROOT, 0777, true);
	}
	catch (ErrorException $e) {
		die('kernel panic: insufficient privileges to ' . PHOEBIUS_TMP_ROOT);
	}
}

$phoebiusNamespaces = array(
	'Core',
	'Core/Exceptions',
	'Core/Patterns',
	'Core/Types',
	'Core/Types/Complex',

	'Mvc',
	'Mvc/ActionResults',
	'Mvc/Exceptions',

	'Orm',
	'Orm/Dao',
	'Orm/Dao/Relationship',
	'Orm/Domain',
	'Orm/Domain/CodeGenerator',
	'Orm/Domain/Notation',
	'Orm/Domain/Notation/Xml',
	'Orm/Exceptions',
	'Orm/Model',
	'Orm/Query',
	'Orm/Query/Projections',
	'Orm/Types',

	'Utils',
	'Utils/Cipher',
	'Utils/Log',
	'Utils/Net',
	'Utils/Stream',
	'Utils/Xml',

	'Dal',
	'Dal/DB',
	'Dal/DB/Exceptions',
	'Dal/DB/Generator',
	'Dal/DB/Query',
	'Dal/DB/Schema',
	'Dal/DB/Sql',
	'Dal/DB/Transaction',
	'Dal/DB/Type',
	'Dal/Expression',
	'Dal/Expression/LogicalOperators',

	'Dal/Cache',
	'Dal/Cache/Peers',

	'Web',
	'Web/Form',
	'Web/Form/controls',
	'Web/UrlRouting',
	'Web/Exceptions',

	// OQL2
	'OQL2',
	'OQL2/Grammar',
	'OQL2/Parsers',
	'OQL2/Parsers/Tokenizer',
	'OQL2/SyntaxTree',
	'OQL2/SyntaxTree/Criteria',
	'OQL2/SyntaxTree/Iterators',
	'OQL2/SyntaxTree/Mutators',
	'OQL2/SyntaxTree/Statements',
);
foreach ($phoebiusNamespaces as $namespace) {
	set_include_path(
		PHOEBIUS_BASE_ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $namespace
		. PATH_SEPARATOR . get_include_path()
	);
}

require_once 'AutoClassLoader.class.php';
require_once 'IncludePathClassLoader.class.php';

AutoClassLoader::getInstance()
	->addLoader(new IncludePathClassLoader())
	->register();
