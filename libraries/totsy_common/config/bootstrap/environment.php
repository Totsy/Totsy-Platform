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
		case 'admin.totsy.com':
		case 'totsy.com':
		case 'www.totsy.com':
		case 'totsystaging.com':
		case 'www.totsystaging.com':
		case 'newprod.totsy.com':
		case '50.56.49.10':
		case 'web1-dc1.totsy.com':
		case 'web2-dc1.totsy.com':
		case 'web3-dc1.totsy.com':
		case 'web4-dc1.totsy.com':
		case 'web5-dc1.totsy.com':
		case 'web6-dc1.totsy.com':
		case 'web7-dc1.totsy.com':
		case 'web8-dc1.totsy.com':
		case 'www.totsystaging.com':
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
 * Setup testing environment variables. `browser*` settings are used within *
 * selenium tests. Please note that `*chrome` will select Firefox as a browser
 * not as one would expect Google Chrome.
 */

$dev = array(
	'browser' => '*chrome',
	'browserUrl' => 'http://totsy'
);
Environment::set('test', $dev);
Environment::set('local', $dev);

?>