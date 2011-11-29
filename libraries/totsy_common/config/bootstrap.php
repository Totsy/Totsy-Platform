<?php

/**
 * Error handling.
 */
require __DIR__ . 'bootstrap/error.php';

/**
 * This file defines bindings between classes which are triggered during the request cycle, and
 * allow the framework to automatically configure its environmental settings. You can add your own
 * behavior and modify the dispatch cycle to suit your needs.
 */
require __DIR__ . '/bootstrap/action.php';

/**
 * This file contains configurations for handling different content types within the framework,
 * including converting data to and from different formats, and handling static media assets.
 */
// require __DIR__ . '/bootstrap/media.php';

require __DIR__ . '/bootstrap/payments.php';

require __DIR__ . '/bootstrap/mail.php';

?>