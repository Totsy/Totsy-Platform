<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\net\http\Media;
use lithium\net\http\Router;
use lithium\core\Environment;
use lithium\storage\Session;
use app\models\File;
use lithium\action\Response;

/**
 * The following allows up to serve images right out of mongodb.
 * This needs to be first so that we don't get a controller error.
 *
 */

Router::connect("/image/{:id:[0-9a-f]{24}}.{:type}", array(), function($request) {
	$request->type = ($request->type == 'jpg') ? 'jpeg' : $request->type;


	if ($file = File::first($request->id)) {
		$bytes = $file->file->getBytes();
	} else {
		return new Response(array('status' => 404));
	}

	return new Response(array(
		'headers' => array(
			'Content-type' => "image/{$request->type}",
			'Pragma' => 'cache',
			'Expires' => date("r", strtotime("+10 years")),
			'Cache-control' => 'max-age=999999',
			'Last-modified' => 'Mon, 29 Jun 1998 02:28:12 GMT'
		),
		'body' => $bytes
	));
});


Router::connect('/api/help/{:args}', array('controller' => 'API', 'action' => 'help'));
Router::connect('/api/{:args}', array('controller' => 'API', 'action' => 'index'));

//Unsubcentral Functions
Router::connect('/unsubcentral/unsubscribed/{:args}', array('controller' => 'unsubcentral', 'action' => 'unsubscribed'));
Router::connect('/unsubcentral/del', array('controller' => 'unsubcentral', 'action' => 'del'));

Router::connect('/login', 'Users::login');
Router::connect('/publicpassword', 'Users::publicpassword');
Router::connect('/register', 'Users::register');
//Router::connect('/register/facebook', 'Users::fbregister');
Router::connect('/momoftheweek', 'MomOfTheWeeks::index');
Router::connect('/momoftheweek/fbml', 'MomOfTheWeeks::fbml');
Router::connect('/surveys', 'Surveys::index');
Router::connect('/invitation/{:args}', 'Users::register');
Router::connect('/join/{:args}', 'Users::register');
Router::connect('/affiliate/{:args}', 'Affiliates::registration');
Router::connect('/a/{:args:[a-zA-Z0-9&\?\.=:/]+}', 'Affiliates::register');

Router::connect('/category/{:args}', 'Events::category');
Router::connect('/age/{:args}', 'Events::age');
//Router::connect('/brands', 'Brands::index'); // commented this out as quicky fix to disable jojo
//Router::connect('/brands/{:args}', 'Brands::view'); // commented this out as quicky fix to disable jojo

Router::connect('/reset', 'Users::reset');
Router::connect('/pages/{:args}', 'Pages::view');
/*Router::connect('/pages/password', 'Users::password');*/

Router::connect('/livingsocial', array('Pages::view', 'args' => array('living_social')));
Router::connect('/blog', 'Blog::index');
Router::connect('/feeds/{:args}', 'Feeds::home');

/** Shopping Cart Routes **/
Router::connect('/checkout/view', 'Cart::view');
Router::connect('/checkout/shipping', 'Orders::shipping');
Router::connect('/checkout/payment', 'Orders::payment');
Router::connect('/checkout/review', 'Orders::review');

Router::connect('/', 'Users::register');
Router::connect('/sales/{:args}', 'Events::index');
Router::connect('/{:action:login|logout}', array('controller' => 'users'));
Router::connect('/addresses', 'Addresses::view');
Router::connect('/addresses/edit{:args}', 'Addresses::edit');
Router::connect('/account/info', 'Users::info');
Router::connect('/account/credits', 'Credits::view');
Router::connect('/account/invites', 'Users::invite');
Router::connect('/account/password', 'Users::password');
Router::connect('/upgrade', 'Users::upgrade');
Router::connect('/events/view/{:item:[a-z0-9\-]+}', 'Events::view');
Router::connect('/welcome', 'Users::affiliate');
Router::connect('/sale/{:event:[a-z0-9\-]+}', 'Events::view');
Router::connect('/sale/{:event:[a-z0-9\-]+}/{:item:[a-z0-9\-]+}', 'Items::view');
Router::connect('/feeds/{:partner:[a-z0-9\-]+}',array(
    'Feeds::home',
    'type' => 'xml'
));
/**
* Taking this route out, as the menu helper is not ready
* for custom routes.
*/

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

/* affiliate routing for categories and affiliates in an URL */
Router::connect('/{:category:[a-z_]+}', array(), function($request) {
   if (!isset($request->query['a']) || !preg_match('/^[a-z_]+$/', $request->query['a'])) {
       return false;
   }
   $request->params = array(
       'controller' => 'affiliates',
       'action' => 'register',
       'args' => array($request->query['a'], $request->category)
   );

   return $request;
});

/**
 * Finally, connect the default routes.
 */

Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}');
Router::connect('/{:controller}/{:action}/{:args}');

?>
