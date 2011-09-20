<?php

use lithium\net\http\Media;
use lithium\action\Response;
use lithium\action\Dispatcher;
use lithium\analysis\Logger;
use lithium\core\Environment;
use admin\extensions\ErrorHandler;
ini_set('display_errors', 0);

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

	Media::render($response, compact('info', 'params'), array(
		'layout' => 'base',
		'controller' => '_errors',
		'template' => 'exception'
	));
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

		return new Response(array(
			'request' => $params['request'],
			'status' => 404,
			'body' => 'Not found.'
		));
	}
);

/**
 * This configuration handles logging PHP errors (notices/warnings only, not exceptions).
 */
ErrorHandler::config(array(
	'logger' => array('handler' => function($info) {
		$keys = array('message', 'file', 'line', 'context', 'stack');
		Logger::notice(json_encode(array_intersect_key($info, array_combine($keys, $keys))));
		return true;
	})
));
ErrorHandler::run(array('trapErrors' => true, 'convertErrors' => false));

?>