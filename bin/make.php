#!/usr/bin/php
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

function message($message)
{
	echo $message, PHP_EOL;
}

function help()
{
	echo <<<EOT

Usage:

  $ make.php [options] [domain-schema.xml]

If domain-schema.xml is not specified then make.php uses var/domain.xml.


General options:

  --config=<path>        path to the configuration. By default make.php tries ./config.inc.php.

  --dry-run              modify nothing, show the results only. Currently not implemented.

  --help, -h             show this help.


Code generator options:

  --code                 generate and ORM over the defined schema: an auxiliary PHP classes
                         and business-entities.

  --regenerate-public    regenerate public files if already generated. This forces a --code option
                         to be switched on automatically.

  --public-dir=<dir>     write generated public class' files to <dir>. Default is lib/Domain.
                         Path is treated as relative to the application directory (\$app).

  --auto-dir=<dir>       write generated internal class' files to <dir>. Default is var/lib/Domain.
                         Path is treated as relative to the application directory (\$app).


Database schema generator options:

  --schema               generate database schema. You should set the name of the database config
                         either in domain-schema.xml (<domain db-schema="<name>">) or
                         using the --db option explicitly.

  --db=<name>            use this database to generate schema (should be added to DBPool). This
                         can be set implicitly in domain-schema.xml (<domain db-schema="<name>">).
                         This forces a --schema option to be switched on automatically.

  --import               import schema to the database. Currently not implemented.

  --schema-file=<file>   write database schema to <file>.
                         Default is var/db/<db_driver>-<domain-schema_name>.sql.
                         Path is treated as relative to the application directory (\$app).
                         This forces a --schema option to be switched on automatically.

Database schema difference generator options:

  --diff                 generate database schema difference. You must specify an XML file
                         with the domain to compare to (--compare) and file to write
                         SQL instructions to (--diff-file).

  --compare=<schema>     domain XML to compare against.
                         This forces a --diff option to be switched on automatically.

  --diff-file=<file>     write SQL difference script to <file>.
                         This forces a --diff option to be switched on automatically.

EOT;
}

function stop($message = null)
{
	if ($message) {
		message($message);
	}

	echo <<<EOT

Use

  $ {$GLOBALS['argv'][0]} --help

for more information.


EOT;

	//help();

	exit(1);
}

$config = null;
$dryRun = false;

$code = false;
$regeneratePublic = false;
$publicDir = 'lib/Domain';
$autoDir = 'var/lib/Domain';

$schema = false;
$db = null;
$import = false;
$schemaFile = null;
$dbObject = null;

$diff = false;
$diffDomainFile = null;
$diffSqlFile = null;


$args = $argv;
array_shift($args);
foreach ($args as $arg) {
	if ($arg{0} == '-') {
		if (strpos($arg, '=')) {
			list ($k, $v) = explode('=', $arg, 2);
		}
		else {
			$k = $arg;
			$v = null;
		}

		switch ($k) {

			//
			// general
			//

			case '--config': {
				$config = $v;
				break;
			}

			case '--dry-run': {
				$dryRun = true;
				break;
			}

			case '--help':
			case '-h': {
				help();

				exit;
			}

			//
			// code generator
			//

			case '--code': {
				$code = true;
				break;
			}

			case '--regenerate-public': {
				$code = true;
				$regeneratePublic = true;
				break;
			}

			case '--public-dir': {
				$publicDir = $v;
				break;
			}

			case '--auto-dir': {
				$autoDir = $v;
			}

			//
			// schema generator
			//

			case '--schema': {
				$schema = true;
				break;
			}

			case '--db': {
				$schema = true;
				$db = $v;
				break;
			}

			case '--import': {
				$import = true;
				break;
			}

			case '--schema-file': {
				$schema = true;
				$schemaFile = $v;
				break;
			}

			//
			// diff
			//

			case '--diff': {
				$diff = true;
				break;
			}

			case '--compare': {
				$diff = true;
				$diffDomainFile = $v;
				break;
			}

			case '--diff-file': {
				$diff = true;
				$diffSqlFile = $v;
				break;
			}

			default: {
				stop('Unknown option '. $k);
			}
		}
	}
}

if (!$code && !$schema) {
	stop('Nothing to do: neither --code nor --schema is set');
}

if (sizeof($args) && $args[sizeof($args)-1]{0} != '-') {
	$xmlSchema = end($args);
}
else {
	message('Domain schema is not specified, trying to pick var/domain.xml');
	$xmlSchema = 'var/domain.xml';
}

$xmlSchema = realpath($xmlSchema);

if (!$xmlSchema) {
	stop ('Domain schema not found');
}

// loading config
if ($config) {
	$config = realpath($config);

	if (!$config) {
		stop ('Config not found');
	}
}
else {
	message('Config is not specified...');
	message('Trying to pick initalize.inc.php...');
	$config = getcwd() . DIRECTORY_SEPARATOR . 'initalize.inc.php';
	if (!file_exists($config)) {
		message('Failed, using the default config.');
		$config = realpath(dirname(__FILE__) . '/../initalize.inc.php');
	}
}

message ('Loading config: ' . $config);
require $config;

message ('Loading schema: ' . $xmlSchema);
$domainBuilder = new XmlOrmDomainBuilder($xmlSchema);

try {

	message ('Building domain...');
	$ormDomain = $domainBuilder->build();

	if ($code) {
		foreach (array('publicDir', 'autoDir') as $dir) {
			$$dir = getcwd() . '/' . $$dir;
			if (!is_dir($$dir)) {
				mkdir($$dir, 0755, true);
			}
		}

		message ('Generating classes...');
		$generator = new OrmGenerator($autoDir, $publicDir);

		if ($regeneratePublic) {
			$generator->regeneratePublic();
		}

		$generator->generate($ormDomain);
	}

	if ($schema) {
		if (!$db && $ormDomain->getDbSchema()) {
			$db = $ormDomain->getDbSchema();
		}

		if ($db) {
			try {
				$dbObject = DBPool::get($db);
			}
			catch (ArgumentException $e) {
				stop ("Unknown database reference: $db");
			}
		}
		else {
			stop ('You should specify db to use');
		}

		if (!$schemaFile) {
			$schemaFile =
				getcwd()
				. DIRECTORY_SEPARATOR . 'var'
				. DIRECTORY_SEPARATOR . 'db'
				. DIRECTORY_SEPARATOR
				. strtolower($dbObject->getDialect()->getDBDriver()->getValue())
				. '-' . pathinfo($xmlSchema, PATHINFO_FILENAME) . '.sql';

			$dir = dirname($schemaFile);
			if (!is_dir($dir)) {
				mkdir($dir, 0755, true);
			}
		}

		message('Building SQL schema...');
		$schemaBuilder = new DBSchemaBuilder($ormDomain);

		$schemaConstructor = new SqlSchemaConstructor($schemaBuilder->build());

		message('Writing SQL schema to '. $schemaFile);
		$schemaConstructor
			->make(
				new FileWriteStream($schemaFile),
				$dbObject->getDialect()
			);
	}

	if ($diff) {
		message('Making diff...');

		$diffDomainFile = realpath($diffDomainFile);
		if (!$diffDomainFile) {
			stop ("Diff domain file is not specified");
		}

		if (!$diffSqlFile) {
			stop ("Diff schema file is not specified");
		}
		$dir = dirname($diffSqlFile);
		if (!is_dir($dir))
			mkdir ($dir, 0755, true);

		if (!$db && $ormDomain->getDbSchema()) {
			$db = $ormDomain->getDbSchema();
		}

		if ($db) {
			try {
				$dbObject = DBPool::get($db);
			}
			catch (ArgumentException $e) {
				stop ("Unknown database reference: $db");
			}
		}
		else {
			stop ('You should specify db to use');
		}

		message ('Loading diff schema: ' . $diffDomainFile);
		$domainBuilder = new XmlOrmDomainBuilder($diffDomainFile);
		$diffOrmDomain = $domainBuilder->build();

		message ('Writing diff to ' . $diffSqlFile);
		$dbDiff = new DBDiff();

		$schemaBuilderOld = new DBSchemaBuilder($diffOrmDomain);
		$schemaOld = $schemaBuilderOld->build();
		$schemaBuilderNew = new DBSchemaBuilder($ormDomain);
		$schemaNew = $schemaBuilderNew->build();

		$dbDiff->make($schemaOld, $schemaNew);
		file_put_contents($diffSqlFile, $dbDiff->toDialectString($dbObject->getDialect()));
	}
}
catch (OrmModelIntegrityException $e) {
	stop ($e->getMessage());
}

echo 'Done', PHP_EOL;
