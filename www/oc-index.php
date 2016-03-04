<?php

if (!defined('OPEN_CART_ENABLE')) {
	exit; // kill if not opened from Nette
}

// Version
define('VERSION', '2.2.0.1b');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

$application_config = 'catalog';

// Application
require_once(DIR_SYSTEM . 'framework.php');
