<?php
/* 
 * Sets up the Felix Online environment 
 */

date_default_timezone_set('Europe/London');

// define current working directory
if(!defined('BASE_DIRECTORY')) define('BASE_DIRECTORY', dirname(__FILE__));
if(!defined('CACHE_DIRECTORY')) define('CACHE_DIRECTORY', BASE_DIRECTORY.'/cache/');

// Composer
require BASE_DIRECTORY.'/vendor/autoload.php';

require_once(BASE_DIRECTORY.'/inc/ez_sql_core.php');
require_once(BASE_DIRECTORY.'/inc/ez_sql_mysqli.php');
require_once(BASE_DIRECTORY.'/inc/SafeSQL.class.php');
require_once(BASE_DIRECTORY.'/glue.php');
$config = require_once(BASE_DIRECTORY.'/inc/config.inc.php');
require_once(BASE_DIRECTORY.'/inc/const.inc.php');
require_once(BASE_DIRECTORY.'/inc/functions.inc.php'); // TODO move to utilities
require_once(BASE_DIRECTORY.'/inc/validator.inc.php');
require_once(BASE_DIRECTORY.'/inc/is_email.inc.php');

/*
 * Models
 */
foreach (glob(BASE_DIRECTORY.'/core/*.php') as $filename) {
	require_once($filename);
}

//require_once(BASE_DIRECTORY.'/inc/authentication.php');
require_once(BASE_DIRECTORY.'/inc/rss.inc.php');

// Initialize App
$app = new \FelixOnline\Core\App($config);

$app['db'] = $db;
$app['safesql'] = $safesql;

if (LOCAL) { // development connector
	// Initialize Akismet
	$connector = new \Riv\Service\Akismet\Connector\Test();
	$app['akismet'] = new \Riv\Service\Akismet\Akismet($connector);

	// Initialize email
	$transport = \Swift_NullTransport::newInstance();
	$app['email'] = \Swift_Mailer::newInstance($transport);

	// Don't cache in local mode
	$app['cache'] = new \Stash\Pool();
}

// Initialize Sentry
$app['sentry'] = new \Raven_Client($app->getOption('sentry_dsn', NULL));

$app->run();
