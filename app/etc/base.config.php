<?php

return array (
	'db' => array(
		'default' => array(
			'adapter' => 'postgresql',
			'host' => 'localhost',
			'user' => 'dbuser',
			'pass' => 'dbpass',
			'name' => 'dbname',
			'default' => true,
		)
	),

	'defaultRouteData' => array('controller' => 'index', 'action' => 404),
	'routes' => array(
		'aux' => array('pattern' => '/smth', 'data' => array()),
	)
);