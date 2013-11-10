<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Chat Service Console',

	// preloading 'log' component
	'preload'=>array('log'),

	'commandMap'=>array(
		'database' => array(
			'class' => 'application.extensions.database-command.EDatabaseCommand',
		),
	),

	// application components
	'components'=>array(
		'db'=>require(__DIR__ . "/dbDefault.php"),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
);