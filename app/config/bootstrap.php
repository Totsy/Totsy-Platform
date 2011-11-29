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
 * Sets up environment detection.
 */
require __DIR__ . '/bootstrap/environment.php';

/**
 * Setup testing environment variables. `browser*` settings are used within *
 * selenium tests. Please note that `*chrome` will select Firefox as a browser
 * not as one would expect Google Chrome.
 */
Environment::set('test', array(
	'browser' => '*chrome',
	'browserUrl' => 'http://totsy'
));


/**
 * Include this file if your application uses a database connection.
 */
require __DIR__ . '/bootstrap/connections.php';

/**
 * Error handling.
 */
require __DIR__ . '/bootstrap/errors.php';

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
 * This file configures console filters and settings, specifically output behavior and coloring.
 */
// require __DIR__ . '/bootstrap/console.php';

require __DIR__ . '/bootstrap/payments.php';

require __DIR__ . '/bootstrap/avatax.php';

/**
<<<<<<< feature/upgrade-pre
 * This file configures the analysis behavior which includes Logging.
 */
require __DIR__ . '/bootstrap/analysis.php';

/**
=======
>>>>>>> HEAD~162
 * Auth and action protection filters.
 */
require __DIR__ . '/bootstrap/auth.php';

/**
 * This file contains configuration for session (and/or cookie) storage, and user or web service
 * authentication.
 */
require __DIR__ . '/bootstrap/session.php';

?>
