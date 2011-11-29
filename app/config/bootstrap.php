<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * This is the primary bootstrap file of your application, and is loaded immediately after the front
 * controller (`webroot/index.php`) is invoked. It includes references to other feature-specific
 * bootstrap files that you can turn on and off to configure the services needed for your
 * application.
 *
 * Besides global configuration of external application resources, these files also include
 * configuration for various classes to interact with one another, usually through _filters_.
 * Filters are Lithium's system of creating interactions between classes without tight coupling. See
 * the `Filters` class for more information.
 *
 * If you have other services that must be configured globally for the entire application, create a
 * new bootstrap file and `require` it here.
 *
 * @see lithium\util\collection\Filters
 */

/**
 * The libraries file contains the loading instructions for all plugins, frameworks and other class
 * libraries used in the application, including the Lithium core, and the application itself. These
 * instructions include library names, paths to files, and any applicable class-loading rules. This
 * file also statically loads common classes to improve bootstrap performance.
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
		case 'totsystaging.com':
		case 'www.totsystaging.com':
		case 'newprod.totsy.com':
		case '50.56.49.10':
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
require __DIR__ . '/bootstrap/connections.php';


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
 * This file configures the analysis behavior which includes Logging.
 */
require __DIR__ . '/bootstrap/analysis.php';

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
		'adapter' => 'app\extensions\adapter\session\Model',
		'model' => 'MongoSession'
	),
	'cookie' => array(
		'adapter' => 'Cookie',
		'expire' => '+30 days'
	)
));

?>
