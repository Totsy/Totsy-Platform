<?php

/**
 * Sets up environment detection.
 */
require __DIR__ . '/bootstrap/environment.php';


/**
 * Error handling.
 */
require __DIR__ . '/bootstrap/error.php';

/**
 * This file configures console filters and settings, specifically output behavior and coloring.
 */
// require __DIR__ . '/bootstrap/console.php';

/**
 * This file contains your application's globalization rules, including inflections,
 * transliterations, localized validation, and how localized text should be loaded. Uncomment this
 * line if you plan to globalize your site.
 */
require __DIR__ . '/bootstrap/g11n.php';

/**
 * This file contains configurations for connecting to external caching resources, as well as
 * default caching rules for various systems within your application
 */
require __DIR__ . '/bootstrap/cache.php';

/**
 * This file defines bindings between classes which are triggered during the request cycle, and
 * allow the framework to automatically configure its environmental settings. You can add your own
 * behavior and modify the dispatch cycle to suit your needs.
 */
require __DIR__ . '/bootstrap/action.php';

/**
 * This file configures the analysis behavior which includes Logging.
 */
require __DIR__ . '/bootstrap/analysis.php';

/**
 * This file contains configurations for handling different content types within the framework,
 * including converting data to and from different formats, and handling static media assets.
 */
 require __DIR__ . '/bootstrap/media.php';

require __DIR__ . '/bootstrap/avatax.php';

require __DIR__ . '/bootstrap/payments.php';

require __DIR__ . '/bootstrap/mail.php';

?>