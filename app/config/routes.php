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
Router::connect("/image/{:id:[0-9a-f]{24}}.jpg", array(), function($request) {
     return new Response(array(
          'headers' => array('Content-type' => 'image/jpg'),
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
/**
 * Redirect all non-authenticated users to 
 */
if(!Session::check('userLogin')) {
	Router::connect('/{:args}', 'Users::login');	
}
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'view', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.html.php)...
 */
Router::connect('/', 'Events::index');
Router::connect('/login', 'Users::login');
Router::connect('/logout', 'Users::logout');
Router::connect('/register', 'Users::register');
Router::connect('/addresses', 'Addresses::view');
Router::connect('/account/add/{:args}', 'Account::add');

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

?>