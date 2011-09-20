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
if (!include LITHIUM_LIBRARY_PATH . '/lithium/core/Libraries.php') {
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
 * This should go into its own file.
 */

use lithium\core\Environment;

Environment::is(function($request) {
	switch ($request->env('HTTP_HOST')) {
		case 'totsy.com':
		case 'www.totsy.com':
		case 'web1-dc1.totsy.com':
		case 'web2-dc1.totsy.com':
		case 'web3-dc1.totsy.com':
		case 'web4-dc1.totsy.com':
		case 'web5-dc1.totsy.com':
		case 'web6-dc1.totsy.com':
		case 'web7-dc1.totsy.com':
		case 'web8-dc1.totsy.com':
		case 'totsystaging.com':
		case 'www.totsystaging.com':
		case 'newprod.totsy.com':
			return 'production';
		case 'test.totsy.com':
			return 'test';
		case 'dev.totsy.com':
			return 'development';
		default:
			return 'local';
	}
});

/**
 * Include this file if your application uses a database connection.
 */
require __DIR__ . '/connections.php';


/**
 * Error handling.
 */
require __DIR__ . '/bootstrap/errors.php';

/**
 * This file defines bindings between classes which are triggered during the request cycle, and
 * allow the framework to automatically configure its environmental settings. You can add your own
 * behavior and modify the dispatch cycle to suit your needs.
 */
require __DIR__ . '/bootstrap/action.php';

/**
 * This file contains configurations for connecting to external caching resources, as well as
 * default caching rules for various systems within your application
 */
require __DIR__ . '/bootstrap/cache.php';

/**
 * This file contains your application's globalization rules, including inflections,
 * transliterations, localized validation, and how localized text should be loaded. Uncomment this
 * line if you plan to globalize your site.
 */
require __DIR__ . '/bootstrap/g11n.php';

/**
 * This file contains configurations for handling different content types within the framework,
 * including converting data to and from different formats, and handling static media assets.
 */
// require __DIR__ . '/bootstrap/media.php';

/**
 * This file configures console filters and settings, specifically output behavior and coloring.
 */
// require __DIR__ . '/bootstrap/console.php';

require __DIR__ . '/bootstrap/payments.php';

require __DIR__ . '/bootstrap/mail.php';

require __DIR__ . '/bootstrap/avatax.php';

/**
 * This configures your session storage. The Cookie storage adapter must be connected first, since
 * it intercepts any writes where the `'expires'` key is set in the options array.
 */
use lithium\storage\Session;

Session::config(array(
 	'default' => array('adapter' => 'app\extensions\adapter\session\Model', 'model' => 'MongoSession'),
 	'cookie' => array('adapter' => 'Cookie', 'expire' => '+30 days')
));

use lithium\security\Auth;
Auth::config(array('userLogin' => array(
	'model' => 'User',
	'adapter' => 'Form',
	'fields' => array('email', 'password')
)));

?>
