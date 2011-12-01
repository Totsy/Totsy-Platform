<?php
/**
 * Configure environment for local testing when running on the command line
 */
use lithium\test\Dispatcher;
use lithium\core\Environment;

Dispatcher::applyFilter('run', function($self, $params, $chain) {
	$environment = Environment::get();

	if ($environment == 'local') {
		// fake some variables to workaround php alerts
		$_SERVER['REQUEST_URI'] = null;
		$_SERVER['HTTP_HOST'] = null;
		$_SERVER['SERVER_NAME'] = null;

		$params['report']->run();
		return $params['report'];
	} else {
		return $chain->next($self, $params, $chain);
	}
});


?>