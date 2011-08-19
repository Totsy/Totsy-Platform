<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\net\http\Router;
use lithium\core\Environment;
use app\models\File;
use lithium\action\Response;

/**
 * The following allows up to serve images right out of MongoDB's GridFS.
 * This needs to be first so that we don't get a controller error. The md5 of
 * each file object will be used to generate the _etag_ for the response. This
 * enables HTTP caching while still giving full control in case the resource for
 * this URL changes.
 */
Router::connect("/image/{:id:[0-9a-f]{24}}.{:type}", array(), function($request) {
	if (!$file = File::first($request->id)) {
		return new Response(array('status' => 404));
	}
	if ($etag = $request->get('http:if_none_match')) {
		if ($file->md5 == trim($etag, '"')) {
			return new Response(array('status' => 304));
		}
	}
	return new Response(array(
		'headers' => array(
			'Content-length' => $file->file->getSize(),
			'Content-type' => $file->mimeType(),
			'Etag' => '"' . $file->md5  . '"',
			'Pragma' => 'cache',
            'Expires' => date("r", strtotime("+10 years")),
            'Cache-control' => 'max-age=999999',
            'Last-modified' => 'Mon, 29 Jun 1998 02:28:12 GMT'
		),
		'body' => $file->file->getBytes()
	));
});

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

Router::connect('/api/help/{:args}', array('controller' => 'API', 'action' => 'help'));
Router::connect('/api/{:args}', array('controller' => 'API', 'action' => 'index'));

//Unsubcentral Functions
Router::connect('/unsubcentral/unsubscribed/{:args}', array('controller' => 'unsubcentral', 'action' => 'unsubscribed'));
Router::connect('/unsubcentral/del', array('controller' => 'unsubcentral', 'action' => 'del'));

Router::connect('/login', 'Users::login');
Router::connect('/register', 'Users::register');
Router::connect('/register/facebook', 'Users::fbregister');
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

Router::connect('/', 'Events::index');
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

/**
 * Finally, connect the default routes.
 */
Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9a-f]{24}}');
Router::connect('/{:controller}/{:action}/{:args}');
?>
