<?php

use lithium\net\http\Media;
use lithium\action\Response;
use lithium\analysis\Logger;
use lithium\core\ErrorHandler;
use lithium\net\http\Router;
use lithium\core\Environment;
use lithium\util\String;
use lithium\storage\Session;

/**
 * This configuration handles 500 pages.
 */
ErrorHandler::apply('lithium\action\Dispatcher::run', array('type' => 'Exception'),
    function($info, $params) {
        $url = $params['request']->url;
        // @TODO - @DG: use "not production" ! ONLY when working on dev
        //if (!Environment::is('production')) {
        if (Environment::is('production')) {
            /* Do we want to provide any kind of info except a blank page? */
            $inc = 0;
            $message = "PAGE ERROR OCCURED ON: /{$params['request']->url} \n";
            $message .= "TIMESTAMP: " . date("M/d/Y H:i:s") . "\n";
            $message .= String::insert('EXCEPTION: {:message} on line {:line} of file {:file}.', $info);
            $message .= "\nTrace: \n";
            foreach($info['trace'] as $trace) {
                $message .= $inc . ". ";
                $message .= String::insert('Called {:function} function from {:class} class in {:file} on line {:line}.', $trace);
                $message .= "\n arguments: ";
                $message .= json_encode($trace['args']);
                $message .= "\n";
                ++$inc;
            }
            $request = $params['request'];
            Logger::error($message);

            $response = new Response(array(
                'status' => 500,
                'request' => $request));
             if (strpos($url, 'image/') === false) {
                $response->body(Media::render($response, compact('info', 'params'), array(
                    'layout' => null,
                    'controller' => '_error',
                    'template' => '500' // 500 is for Internal Server Error ("woopsies"); 503-maint is Maintenance page
                )));
            }
            // @TODO - @DG: add back in mail function (comment it out when working on dev to prevent annoying the bugs list folks )
            mail('bugs@totsy.com', "500 Error on /{$params['request']->url}", $message);
        } else {
            /* Full post mortem in non-production envs. */
            $request = $params['request'];
            $response = new Response(array(
                'status' => 500,
                'request' => $request));
             if (strpos($url, 'image/') === false) {
                $response->body(Media::render($response, compact('info', 'params'), array(
                    'layout' => null,
                    'controller' => '_error',
                    'template' => 'exception'
                )));
            }
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
			$response->headers = array( 'location' => Router::match(array(
				'controller' => 'search', 'action' => 'view', 'search' => $term
			)));
			Logger::debug("Redirecting to 404 search for term `{$term}`.");
		}
		return $response;
	}
);

ErrorHandler::run(array('trapErrors' => true, 'covertErrors' => false));
?>
