<?php

use lithium\action\Response;
use lithium\analysis\Logger;
use lithium\core\ErrorHandler;
use lithium\net\http\Router;

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

?>