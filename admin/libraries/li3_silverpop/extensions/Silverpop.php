<?php
/**
 * li3_silverpop plugin for Lithium: the most rad php framework.
 *
 * @copyright     Copyright 2010, Fitz H. Agard
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_silverpop\extensions;

use li3_silverpop\models\Log;
use lithium\action\Request;
use lithium\template\View;
use lithium\core\Environment;
use lithium\net\http\Service;
use MongoDate;

/**
 * The `Silverpop` class is the single point of entry to integrate with the silverpop API.
 * This API will allow you to send transactional emails using template relationships stored in
 * your application and on silverpop transact.
 *
 * @see lithium\core\Adaptable
 * @see lithium\net\http\Service
 */
class Silverpop extends \lithium\core\Adaptable {

	protected static $_view;

	/**
	 * Renders XML Template.
	 */
	protected static function _view() {
		if (static::$_view) {
			return static::$_view;
		}
		return static::$_view = new View(array(
			'paths' => array(
				'template' => '{:library}/libraries/li3_silverpop/templates/{:template}.xml.php'
			),
			'request' => new Request()
		));
	}

	/**
	 * The `send` method allows you to exchange HTTP/HTTPS url_encoded information to the silverpop transact system.
	 * Transferring with this method is only to be used when the XML
	 * body contains less than 10 recipients. For a batch process use the command line
	 * method to send an FTP file to Silverpop.
	 *
	 * Usage:
	 *{{{
	 * $data = array(
	 *  'email' => 'testuser@test.com', 
	 *  'order_id' => "test0123", 
	 *  'brand_name' => 'Super Lithium Style'
	 * );
	 *
	 * Silverpop::send('test', $data);
	 *}}}
	 *
	 * @param string $template The XML template name.
	 * @param array $data Data being sent to template for rendering.
	 * @return boolean
	 */
	public static function send($template = null, array $data = array()) {
		$http = new Service(static::config('default'));
		try {
			$body = static::_view()->render('all', $data, compact('template'));
			$request = (array) simplexml_load_string($body);
			$response = (array) simplexml_load_string($http->send('post', null, array('xml' => $body)));
			$logResponse = Log::create();
			$data = array(
				'request' => $request,
				'response' => $response,
				'created' => new MongoDate(time())
			);
		} catch (Exception $e) {
			$data = array('Error' => $e);
		}
		return $logResponse->save($data);
	}
}

?>