<?php

use lithium\net\http\Media;
use lithium\action\Response;
use lithium\action\Dispatcher;
use lithium\analysis\Logger;
use lithium\core\Environment;
use lithium\core\ErrorHandler;
use lithium\net\http\Router;
use lithium\util\String;

/**
 * Add an extra `Dispatcher` filter to ensure that errors are hidden in production, but shown in all
 * other cases. Uncomment the commented line to also hide errors in test deployments.
 */
Dispatcher::applyFilter('run', function($self, $params, $chain) {
	// ini_set('display_errors', Environment::is('production') || Environment::is('test') ? 0 : 1);
	ini_set('display_errors', Environment::is('production') ? 0 : 1);
	return $chain->next($self, $params, $chain);
});

ErrorHandler::apply('lithium\action\Dispatcher::run', array(), function($info, $params) {
	$response = new Response(array('status' => 500));

	if (Environment::is('production')) {
		/* Do we want to provide any kind of info except a blank page? */
		// $response->body = 'Internal Server Error';
	} else {
		/* Full post mortem in non-production envs. */
		Media::render($response, compact('info', 'params'), array(
			'layout' => 'base',
			'controller' => '_errors',
			'template' => 'exception'
		));
	}
	return $response;
});

/**
 * This configuration handles 404 pages.
 */
ErrorHandler::apply(
	'lithium\action\Dispatcher::run',
	array('type' => array(
		'lithium\action\DispatchException',
		'lithium\template\TemplateException'
	)),
	function($info, $params) {
		$url = $params['request']->url;
		Logger::debug("Showing 404 for URL `/{$url}`");

		$term = str_replace('/', '', $url);
		$response = new Response(array(
			'request' => $params['request'],
			'status' => 404
		));
		if (strpos($url, 'image/') === false) {
			$response->location = Router::match(array(
				'controller' => 'search', 'action' => 'view', 'search' => $term
			));
			Logger::debug("Redirecting to 404 search for term `{$term}`.");
		}
		return $response;
	}
);

/**
 * This configuration handles logging PHP errors (notices/warnings only, not exceptions).
 */
ErrorHandler::config(array(
	'logger' => array('handler' => function($info) {
		/*
		$keys = array('message', 'file', 'line', 'context', 'stack');
		$message = json_encode(array_intersect_key($info, array_combine($keys, $keys)));
		Logger::notice($message);
		*/

		$message = String::insert('{:message} on line {:line} of file {:file}.', $info);
		Logger::notice($message);
		return true;
	})
));
ErrorHandler::run(array('trapErrors' => true, 'convertErrors' => false));

?>