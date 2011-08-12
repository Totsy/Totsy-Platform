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
Router::connect('/files/dav', 'Files::dav');
Router::connect('/files/dav/{:file:.*}', 'Files::dav');

Router::connect('/login', 'Users::login');
Router::connect('/logout', 'Users::logout');
Router::connect('/token', 'Users::token');
Router::connect('/', 'Dashboard::index');

Router::connect('/register', 'Users::register');
Router::connect('/addresses', 'Addresses::view');
Router::connect('/account/add/{:args}', 'Account::add');

#users
Router::connect('/users/view/{:args}', 'Users::view');
Router::connect('/users/accountStatus/{:args}', 'Users::accountStatus');
Router::connect('/select/event/{:args}', 'Base::selectEvent');
Router::connect('/token', 'Users::token');
#items
Router::connect('/items/preview/{:event:[a-z0-9\-]+}/{:item:[a-z0-9\-]+}', 'Items::preview');
Router::connect('/items/removeItems/', 'Items::removeItems');
Router::connect('/items/images/order/{:item:[a-z0-9\-]+}', 'Items::orderImages');
#events
Router::connect('/events', 'Events::index');
Router::connect('/events/media-status/{:id:[a-z0-9\-]+}', 'Events::media_status');
Router::connect('/banners/media-status/{:id:[a-z0-9\-]+}', 'Banners::media_status');

Router::connect('/files', 'Files::index');
Router::connect('/files/pending', 'Files::pending');
Router::connect('/files/pending/{:on:[a-z0-9\-]+}/{:search_type:(affiliate|event)}', 'Files::pending');
Router::connect('/files/pending/{:on:[a-z0-9\-]+}', 'Files::pending');
Router::connect('/files/orphaned', 'Files::orphaned');
Router::connect('/files/delete/{:id:[0-9a-f]{24}}', 'Files::delete');
Router::connect('/files/rename/{:id:[0-9a-f]{24}}', 'Files::rename');
Router::connect('/files/associate/{:scope:(all|pending|orphaned)}', 'Files::associate');
Router::connect('/files/associate/{:scope:(all|pending|orphaned)}/{:on:[a-z0-9\-]+}', 'Files::associate');
Router::connect('/files/associate/{:scope:(all|pending|orphaned)}/{:on:[a-z0-9\-]+}/{:search_type:(affiliate|event)}', 'Files::associate');
Router::connect('/files/associate/{:id:[0-9a-f]{24}}', 'Files::associate');
Router::connect('/files/upload/{:args}', 'Files::upload');

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
}


?>
