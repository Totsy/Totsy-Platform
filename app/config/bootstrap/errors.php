<?php

use app\extensions\ErrorHandler;
use lithium\action\Response;

$conditions = array('type' => 'lithium\action\DispatchException');

ErrorHandler::apply('lithium\action\Dispatcher', 'run', $conditions, function($e, $params) {
	$request = $params['request'];

	return new Response(compact('request') + array('location' => array(
		'Search::view', 'search' => str_replace('/', '', $request->url)
	)));
});

?>