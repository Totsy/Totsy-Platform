<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\net\http\Router;
use lithium\core\Environment;
use admin\models\File;
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
			'Etag' => '"' . $file->md5  . '"'
		),
		'body' => $file->file->getBytes()
	));
});

/* Files */
Router::connect('/files', 'Files::index');
Router::connect('/files/pending', 'Files::pending');
Router::connect('/files/pending/{:on:[a-z0-9\-]+}', 'Files::pending');
Router::connect('/files/orphaned', 'Files::orphaned');
Router::connect('/files/delete/{:id:[0-9a-f]{24}}', 'Files::delete');
Router::connect('/files/rename/{:id:[0-9a-f]{24}}', 'Files::rename');
Router::connect('/files/associate/{:scope:(all|pending|orphaned)}', 'Files::associate');
Router::connect('/files/associate/{:scope:(all|pending|orphaned)}/{:on:[a-z0-9\-]+}', 'Files::associate');
Router::connect('/files/associate/{:id:[0-9a-f]{24}}', 'Files::associate');
Router::connect('/files/upload/{:args}', 'Files::upload');

/* Users/Account */
Router::connect('/login', 'Users::login');
Router::connect('/logout', 'Users::logout');
Router::connect('/register', 'Users::register');
Router::connect('/token', 'Users::token');
Router::connect('/account/add/{:args}', 'Account::add');
Router::connect('/users/view/{:args}', 'Users::view');
Router::connect('/users/accountStatus/{:args}', 'Users::accountStatus');

/* Events */
Router::connect('/events', 'Events::index');
Router::connect('/select/event/{:args}', 'Base::selectEvent');

/* Items */
Router::connect('/items/view/{:id:[a-z0-9\-]+}', 'Items::view');
Router::connect('/items/preview/{:event:[a-z0-9\-]+}/{:item:[a-z0-9\-]+}', 'Items::preview');
Router::connect('/items/images/order/{:item:[a-z0-9\-]+}', 'Items::orderImages');

/* Other */
Router::connect('/', 'Dashboard::index');
Router::connect('/search/{:search}', 'Search::view');
Router::connect('/addresses', 'Addresses::view');

/* Generic */
Router::connect('/pages/{:args}', 'Pages::view');
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}');
Router::connect('/{:controller}/{:action}/{:args}');

/* Testing */
if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => '\lithium\test\Controller'));
	Router::connect('/test', array('controller' => '\lithium\test\Controller'));
}

?>