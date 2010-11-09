<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

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

Router::connect('/register', 'Users::register');
Router::connect('/momoftheweek', 'MomOfTheWeeks::index');
Router::connect('/momoftheweek/fbml', 'MomOfTheWeeks::fbml');
Router::connect('/invitation/{:args}', 'Users::register');
Router::connect('/join/{:args}', 'Users::register');
Router::connect('/reset', 'Users::reset');
Router::connect('/pages/{:args}', 'Pages::view');
Router::connect('/blog', 'Blog::index');

/**
 * Redirect all non-authenticated users to 
 */
if(!Session::check('userLogin')) {
	Router::connect('/', 'Users::login');
	Router::connect('/{:args}', 'Users::login');
	return;
}

Router::connect('/', 'Events::index');
Router::connect('/{:action:login|logout}', array('controller' => 'users'));

Router::connect('/addresses', 'Addresses::view');
Router::connect('/addresses/edit{:args}', 'Addresses::edit');
Router::connect('/account/info', 'Users::info');
Router::connect('/account/add/{:args}', 'Account::add');
Router::connect('/invite', 'Users::invite');
Router::connect('/shopping/cart', 'Cart::index');
Router::connect('/shopping/checkout.{:type}', 'Orders::add');
Router::connect('/shopping/checkout', 'Orders::add');
Router::connect('/upgrade', 'Users::upgrade');
Router::connect('/events/view/{:item:[a-z0-9\-]+}', 'Events::view');
Router::connect('/welcome', 'Users::affiliate');

/**
* Taking this route out, as the menu helper is not ready
* for custom routes.
*/
//Router::connect('/help', 'Tickets::add');

/**
 * Wire up the "search" for the 404 page, that attempts to figure out what you wanted.
 */
Router::connect('/search/{:search}', 'Search::view');

/**
 * Connect the testing routes.
 */
if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => '\lithium\test\Controller'));
	Router::connect('/test', array('controller' => '\lithium\test\Controller'));
}

/**
 * Finally, connect the default routes.
 */
Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}');
Router::connect('/{:controller}/{:action}/{:args}');

?>