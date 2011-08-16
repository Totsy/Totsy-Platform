<?php

namespace admin\tests\functional;

use Sabre_DAV_Server;
use Sabre_HTTP_Request as Request;
use admin\tests\mocks\extensions\sabre\dav\ResponseMock;
use admin\extensions\sabre\dav\EventsDirectory;
use admin\extensions\sabre\dav\PendingDirectory;
use admin\extensions\sabre\dav\OrphanedDirectory;
use admin\models\File;

class WebDavTest extends \lithium\test\Unit {

	protected $_backup = array();

	public function setUp() {
		$this->_backup['dedupe'] = File::$dedupe;
		File::$dedupe = false;
	}

	public function tearDown() {
		File::$dedupe = $this->_backup['dedupe'];
	}

	public function testAddPending() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';

		$name = uniqid('test_') . '.jpg';
		$stream = fopen($file, 'rb');
		$meta = fstat($stream);

		$root = array(
			new PendingDirectory()
		);
		$server = new Sabre_DAV_Server($root);
		$server->setBaseUri('/files/dav');
		$env = array(
			'REQUEST_URI' => "/files/dav/pending/{$name}",
			'REQUEST_METHOD' => 'PUT',
			'CONTENT_TYPE' => 'image/jpeg',
			'CONTENT_LENGTH' => $meta['size'],
			'HTTP_USER_AGENT' => 'Cyberduck/4.1 (Mac OS X/10.5.8) (i386)',
			'SERVER_PROTOCOL' => 'HTTP/1.1',
			'REMOTE_PORT' => '57213'
		);

		$server->httpRequest = new Request($env);
		$server->httpResponse = new ResponseMock();
		$server->httpRequest->setBody($stream, true);

		$server->exec();

		$expected = 'HTTP/1.1 201 Created';
		$result = $server->httpResponse->status;
		$this->assertEqual($expected, $result);

		$file = File::first(array(
			'conditions' => array(
				'name' => $name
			)
		));
		$result = $file->pending;
		$this->assertTrue($result);

		$file->delete();
	}
}

?>