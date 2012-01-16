<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * The `Collection` class, which serves as the base class for some of Lithium's data objects
 * (`RecordSet` and `Document`) provides a way to manage data collections in a very flexible and
 * intuitive way, using closures and SPL interfaces. The `to()` method allows a `Collection` (or
 * subclass) to be converted to another format, such as an array. The `Collection` class also allows
 * other classes to be connected as handlers to convert `Collection` objects to other formats.
 *
 * The following connects the `Media` class as a format handler, which allows `Collection`s to be
 * exported to any format with a handler provided by `Media`, i.e. JSON. This enables things like
 * the following:
 * {{{
 * $posts = Post::find('all');
 * return $posts->to('json');
 * }}}
 */
use lithium\util\Collection;

Collection::formats('lithium\net\http\Media');

/**
 * Registering image types as well as attaching a simple
 * passthru decode handler to preserve binary data.
 *
 * @see admin\controllers\FilesController
 */
use lithium\action\Dispatcher;
use lithium\action\Response;
use lithium\net\http\Media;

Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
	list($library, $asset) = explode('/', $params['request']->url, 2) + array("", "");

	if ($asset && ($path = Media::webroot($library)) && file_exists($file = "{$path}/{$asset}")) {
		return function() use ($file) {
			$info = pathinfo($file);
			$media = Media::type($info['extension']);
			$content = (array) $media['content'];

			return new Response(array(
				'headers' => array('Content-type' => reset($content)),
				'body' => file_get_contents($file)
			));
		};
	}
	return $chain->next($self, $params, $chain);
});


$decode = function($data) { return $data; };
Media::type('png', 'image/png', compact('decode'));
Media::type('jpg', 'image/jpeg', compact('decode'));
Media::type('gif', 'image/gif', compact('decode'));
Media::type('tiff', 'image/tiff', compact('decode'));
Media::type('txt', 'text/plain', compact('decode'));

Media::type('xml', 'text/xml', array(
    'layout' => '{:library}/views/layouts/default.xml.php',
    'view' => 'lithium\template\View',
    'template' => array(
            '{:library}/views/{:controller}/{:template}.xml.php'
        ),
    'conditions' => array('type' => true),
    'encode' => function($data) {
       return $data;
    }
));
?>
