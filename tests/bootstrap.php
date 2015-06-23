<?php

// The Nette Tester command-line runner can be
// invoked through the command: ../vendor/bin/tester .

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}


// configure environment
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');


// create temporary directory
define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());
@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
Tester\Helpers::purge(TEMP_DIR);


function test(\Closure $function)
{
	$function();
}


function getContainer() {
	$configurator = new Nette\Configurator;

	$configurator->setTempDirectory(__DIR__ . '/tmp');

	$configurator->createRobotLoader()
		->addDirectory(__DIR__ . '/../app')
		->register();

	$configurator->addConfig(__DIR__ . '/../app/config/config.neon');
	$configurator->addConfig(__DIR__ . '/config.local.neon');

	$container = $configurator->createContainer();

	return $container;
}

function lockDatabase() {
	Tester\Environment::lock('database', __DIR__ . '/tmp');
}

function loadDatabaseStructure(\Nette\Database\Context $context) {
	$context->query(file_get_contents(__DIR__ . '/db-struct.sql'));
}
