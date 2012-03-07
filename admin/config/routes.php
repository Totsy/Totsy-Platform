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
Router::connect('/uploads/upload/{:args}', 'Uploads::upload');


Router::connect('/login', 'Users::login');
Router::connect('/logout', 'Users::logout');
Router::connect('/token', 'Users::token');


Router::connect('/', 'Dashboard::index');
Router::connect('/search/{:search}', 'Search::view');


Router::connect('/register', 'Users::register');
Router::connect('/addresses', 'Addresses::view');
Router::connect('/account/add/{:args}', 'Account::add');
Router::connect('/events', 'Events::index');
Router::connect('/users/view/{:args}', 'Users::view');
Router::connect('/users/accountStatus/{:args}', 'Users::accountStatus');
Router::connect('/select/event/{:args}', 'Base::selectEvent');
Router::connect('/items/preview/{:event:[a-z0-9\-]+}/{:item:[a-z0-9\-]+}', 'Items::preview');

Router::connect('promocodes/massPromocodes/{:parent_id:[a-z0-9]+}',array(
    'Promocodes::massPromocodes',
    'type' => 'csv'
));

Router::connect('/pages/{:args}', 'Pages::view');

if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => '\lithium\test\Controller'));
	Router::connect('/test', array('controller' => '\lithium\test\Controller'));
}

Router::connect('/{:controller}/{:action}/{:id:[0-9]+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}');
Router::connect('/{:controller}/{:action}/{:args}');

?>