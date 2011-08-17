<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\core\Libraries;


/**
 * Optimize default request cycle by loading common classes.  If you're implementing custom
 * request/response or dispatch classes, you can safely remove these.  Actually, you can safely
 * remove them anyway, they're just there to give slightly you better out-of-the-box performance.
 */
// require LITHIUM_LIBRARY_PATH . '/lithium/core/Object.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/core/StaticObject.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/util/Collection.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/util/collection/Filters.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/util/Inflector.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/util/String.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/core/Adaptable.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/core/Environment.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/net/http/Message.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/net/http/Media.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/net/http/Request.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/net/http/Response.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/net/http/Route.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/net/http/Router.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/action/Controller.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/action/Dispatcher.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/action/Request.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/action/Response.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/template/View.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/template/view/Renderer.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/template/view/Compiler.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/template/view/adapter/File.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/storage/Cache.php';
// require LITHIUM_LIBRARY_PATH . '/lithium/storage/cache/adapter/Apc.php';

/**
 * Add the Lithium core library.  This sets default paths and initializes the autoloader.  You
 * generally should not need to override any settings.
 */
Libraries::add('lithium');
Libraries::add('li3_flash_message');

/**
 * Add the application.  You can pass a `'path'` key here if this bootstrap file is outside of
 * your main application, but generally you should not need to change any settings.
 */
Libraries::add('admin', array('default' => true));

Libraries::add('li3_payments');
Libraries::add('PEAR', array(
	'prefix' => false,
	'includePath' => true,
	'transform' => function($class, $config) {
		$file = $config['path'] . '/' . str_replace('_', '/', $class) . $config['suffix'];
		return file_exists($file) ? $file : null;
	}
));
Libraries::add('totsy_common');
Libraries::add('li3_fixtures');
Libraries::add('li3_docs');
Libraries::add('Imagine');

require LITHIUM_APP_PATH . '/libraries/phpexcel/PHPExcel.php';
require LITHIUM_APP_PATH . '/libraries/phpexcel/PHPExcel/IOFactory.php';
//require LITHIUM_APP_PATH . '/libraries/swiftmailer/lib/swift_required.php';
require LITHIUM_APP_PATH . '/libraries/FusionCharts/FusionCharts_Gen.php';

?>