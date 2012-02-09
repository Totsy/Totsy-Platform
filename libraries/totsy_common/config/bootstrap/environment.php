<?php

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '20000');

/**
 * Sets up environment detection.
 */
use lithium\core\Environment;

Environment::is(function($request) {
	switch ($request->env('HTTP_HOST')) {
		case 'totsy.com':
		case 'www.totsy.com':
		case 'newprod.totsy.com':
		case 'mamasource.totsy.com':
		case 'admin.totsy.com':
		case '50.56.49.10': // This is the production rackspace load balancer IP
		case 'admin-prod.totsy.com':
			return 'production';
		case 'stage.totsy.com':
		case 'mamasource.totsy.com':
		case 'adminstage.totsy.com':
			return 'staging';
		case 'test.totsy.com':
		case 'admin.totsy.com':
		case '50.57.205.144': // This is the totsystaging load balancer IP
			return 'test';
		case 'dev.totsy.com':
			return 'development';
		default:
			return 'local';
	}
});

/**
 * Setup testing environment variables. `browser*` settings are used within *
 * selenium tests. Please note that `*chrome` will select Firefox as a browser
 * not as one would expect Google Chrome.
 */
Environment::set('test', array(
	'browser' => '*chrome',
	'browserUrl' => 'http://totsy'
));

?>
