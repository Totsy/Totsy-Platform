<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This is the path to the class libraries used by your application, and must contain a copy of the
 * Lithium core.  By default, this directory is named 'libraries', and resides in the same
 * directory as your application.  If you use the same libraries in multiple applications, you can
 * set this to a shared path on your server.
 */
define('LITHIUM_LIBRARY_PATH', dirname(dirname(__DIR__)) . '/libraries');

/**
 * This is the path to your application's directory.  It contains all the sub-folders for your
 * application's classes and files.  You don't need to change this unless your webroot folder is
 * stored outside of your app folder.
 */
define('LITHIUM_APP_PATH', dirname(__DIR__));

/**
 * Locate and load Lithium core library files.  Throws a fatal error if the core can't be found.
 * If your Lithium core directory is named something other than 'lithium', change the string below.
 */
if (!include LITHIUM_APP_PATH . '/libraries/lithium/core/Libraries.php') {
	$message  = "Lithium core could not be found.  Check the value of LITHIUM_LIBRARY_PATH in ";
	$message .= "config/bootstrap.php.  It should point to the directory containing your ";
	$message .= "/libraries directory.";
	trigger_error($message, E_USER_ERROR);
}

/**
 * This file contains the loading instructions for all class libraries used in the application,
 * including the Lithium core, and the application itself. These instructions include library names,
 * paths to files, and any applicable class-loading rules. Also includes any statically-loaded
 * classes to improve bootstrap performance.
 */
require __DIR__ . '/bootstrap/libraries.php';

/**
 * Include this file if your application uses a database connection.
 */
require __DIR__ . '/connections.php';

/**
 * Error-handling.
 */
require __DIR__ . '/bootstrap/error.php';

/**
 * Auth and action protection filters.
 */
require __DIR__ . '/bootstrap/auth.php';

/**
 * This configures your session storage. The Cookie storage adapter must be connected first, since
 * it intercepts any writes where the `'expires'` key is set in the options array.
 */
use lithium\storage\Session;

Session::config(array(
	'default' => array(
		'adapter' => 'admin\extensions\adapter\session\Model',
		'model' => 'MongoSession'
	),
	'flash_message' => array(
		'adapter' => 'admin\extensions\adapter\session\Model',
		'model' => 'MongoSession'
	)
));

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '20000');

?>