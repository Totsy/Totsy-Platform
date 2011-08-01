<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\net\http\Media;
use \lithium\net\http\Router;
use \lithium\core\Environment;
use \lithium\storage\Session;
use app\models\File;
use lithium\action\Response;

/**
 * The following allows up to serve images right out of mongodb.
 * This needs to be first so that we don't get a controller error.
 *
 */
Router::connect("/image/{:id:[0-9a-f]{24}}.{:type}", array(), function($request) {
	$request->type = ($request->type == 'jpg') ? 'jpeg' : $request->type;

	return new Response(array(
		'headers' => array(
			'Content-type' => "image/{$request->type}",
			'Pragma' => 'cache',
			'Expires' => date("r", strtotime("+10 years")),
			'Cache-control' => 'max-age=999999',
			'Last-modified' => 'Mon, 29 Jun 1998 02:28:12 GMT'
		),
		'body' => File::first($request->id)->file->getBytes()
	));
});

/**
 * The following allows up to serve images right out of mongodb.
 * This needs to be first so that we don't get a controller error.
 *
 */
Router::connect("/image/{:id:[0-9a-f]{24}}.gif", array(), function($request) {
     return new Response(array(
          'type' => 'image/gif',
          'body' => File::first($request->id)->file->getBytes()
     ));
});

Router::connect('/api/help/{:args}', array('controller' => 'API', 'action' => 'help'));
Router::connect('/api/{:args}', array('controller' => 'API', 'action' => 'index'));

Router::connect('/register', 'Users::register');
Router::connect('/register/{:locale}', 'Users::register');
Router::connect('/register/facebook', 'Users::fbregister');
Router::connect('/register/{:locale}', 'Users::register');
Router::connect('/momoftheweek', 'MomOfTheWeeks::index');
Router::connect('/momoftheweek/fbml', 'MomOfTheWeeks::fbml');
Router::connect('/surveys', 'Surveys::index');
Router::connect('/invitation/{:args}', 'Users::register');
Router::connect('/join/{:args}', 'Users::register');
Router::connect('/affiliate/{:args}', 'Affiliates::registration');
Router::connect('/a/{:args:[a-zA-Z0-9&\?\.=:/]+}', 'Affiliates::register');
Router::connect('/reset', 'Users::reset');
Router::connect('/pages/{:args}', 'Pages::view');
Router::connect('/livingsocial', array('Pages::view', 'args' => array('living_social')));
Router::connect('/blog', 'Blog::index');
Router::connect('/feeds/{:args}', 'Feeds::home');

/** Shopping Cart Routes **/
Router::connect('/checkout/view', 'Cart::view');
Router::connect('/checkout/shipping', 'Orders::shipping');
Router::connect('/checkout/payment', 'Orders::payment');
Router::connect('/checkout/review', 'Orders::review');

?>