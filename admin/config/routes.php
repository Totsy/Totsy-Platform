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
use admin\models\File;
use lithium\action\Response;

/**
 * The following allows up to serve images right out of mongodb.
 * This needs to be first so that we don't get a controller error.
 *
 */
Router::connect("/image/{:id:[0-9a-f]{24}}.{:type}", array(), function($request) {
	return new Response(array(
		'headers' => array('Content-type' => "image/{$request->type}"),
		'body' => File::first($request->id)->file->getBytes()
	));
});
Router::connect('/uploads', 'Uploads::index');
Router::connect('/uploads/upload{:args}', 'Uploads::upload');

/**
 * Redirect all non-authenticated users to
 */
/*
if (!Session::check('userLogin')) {
	Router::connect('/{:args}', 'Users::login');
}
*/

$session = Session::read('userLogin');

Router::connect('/login', 'Users::login');
Router::connect('/logout', 'Users::logout');

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'view', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.html.php)...
 */
//Router::connect('/', array('Pages::view', 'home'));
/**
 * Leaving above code and comments, but redirecting to Dashboard
 */
Router::connect('/', 'Dashboard::index');

Router::connect('/search/{:search}', 'Search::view');


/**
 * Hooking up ACLs
 */
if (isset($session['acls'])) {
	foreach ($session['acls'] as $acl) {
		$connect = implode('::', array($acl['controller'], $acl['action']));
		Router::connect($acl['route'], $connect);
	}
}

/**
 * Hooking up someone is only an admin.
 */
// if ($session['admin'] && !isset($session['acls'])) {
	Router::connect('/register', 'Users::register');
	Router::connect('/addresses', 'Addresses::view');
	Router::connect('/account/add/{:args}', 'Account::add');
	Router::connect('/events', 'Events::index');
	Router::connect('/users/view/{:args}', 'Users::view');
	Router::connect('/users/accountStatus/{:args}', 'Users::accountStatus');
	Router::connect('/select/event/{:args}', 'Base::selectEvent');
	Router::connect('/items/preview/{:event:[a-z0-9\-]+}/{:item:[a-z0-9\-]+}', 'Items::preview');

	/**
	 * ...and connect the rest of 'Pages' controller's urls.
	 */
	Router::connect('/pages/{:args}', 'Pages::view');

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
	Router::connect('/{:controller}/{:action}/{:id:[0-9]+}.{:type}', array('id' => null));
	Router::connect('/{:controller}/{:action}/{:id:[0-9]+}');
	Router::connect('/{:controller}/{:action}/{:args}');
// }

?>