<?php

namespace admin\tests\functional;

use MongoDate;
use Sabre_DAV_Server;
use Sabre_HTTP_Request as Request;
use admin\tests\mocks\extensions\dav\ResponseMock;
use admin\extensions\dav\EventsDirectory;
use admin\extensions\dav\PendingDirectory;
use admin\extensions\dav\OrphanedDirectory;
use admin\models\Event;
use admin\models\Item;
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

	public function testAddEventLogoImage() {
		$event = Event::create(array(
			'title' => 'Test 1',
			'url' => $eventName = uniqid('test-'),
			'images' => array(),
			'start_date' => new MongoDate(strtotime('2011-08-18 00:00:00'))
		));
		$event->save();

		$root = array(
			new EventsDirectory()
		);
		$server = new Sabre_DAV_Server($root);
		$server->httpResponse = new ResponseMock();
		$server->setBaseUri('/files/dav');

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$fileName = uniqid('test_');
		$stream = fopen($file, 'rb');
		$meta = fstat($stream);

		$env = array(
			'REQUEST_URI' => "/files/dav/events/2011/8/{$eventName}/logo/{$fileName}.jpg",
			'REQUEST_METHOD' => 'PUT',
			'CONTENT_TYPE' => 'text/plain',
			'CONTENT_LENGTH' => $meta['size'],
			'HTTP_USER_AGENT' => 'Cyberduck/4.1 (Mac OS X/10.5.8) (i386)',
			'SERVER_PROTOCOL' => 'HTTP/1.1',
			'REMOTE_PORT' => '57213'
		);

		$server->httpRequest = new Request($env);
		$server->httpRequest->setBody($stream, true);

		$server->exec();

		$expected = 'HTTP/1.1 201 Created';
		$result = $server->httpResponse->status;
		$this->assertEqual($expected, $result);

		$file = File::first(array(
			'conditions' => array(
				'name' => $fileName . '.jpg'
			)
		));
		$this->assertTrue($file);

		$event = Event::first(array(
			'conditions' => array(
				'url' => $eventName
			)
		));
		$result = $event->images['logo_image'];
		$expected = (string) $file->_id;
		$this->assertEqual($expected, $result);

		fclose($stream);
		$event->delete();
		$file->delete();
	}

	public function testAddItemAlternateImage() {
		$item = Item::create(array(
			'title' => 'Test Item 1',
			'url' => $itemName = uniqid('test-'),
			'alternate_images' => array(),
			'start_date' => new MongoDate(strtotime('2011-08-18 00:00:00'))
		));
		$item->save();

		$event = Event::create(array(
			'title' => 'Test Event 1',
			'url' => $eventName = uniqid('test-'),
			'images' => array(),
			'start_date' => new MongoDate(strtotime('2011-08-18 00:00:00')),
			'items' => array(
				(string) $item->_id
			)
		));
		$event->save();


		$root = array(
			new EventsDirectory()
		);
		$server = new Sabre_DAV_Server($root);
		$server->httpResponse = new ResponseMock();
		$server->setBaseUri('/files/dav');

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$fileName = uniqid('test_');
		$stream = fopen($file, 'rb');
		$meta = fstat($stream);

		$env = array(
			'REQUEST_URI' => "/files/dav/events/2011/8/{$eventName}/_items/{$itemName}/alternate/{$fileName}.jpg",
			'REQUEST_METHOD' => 'PUT',
			'CONTENT_TYPE' => 'text/plain',
			'CONTENT_LENGTH' => $meta['size'],
			'HTTP_USER_AGENT' => 'Cyberduck/4.1 (Mac OS X/10.5.8) (i386)',
			'SERVER_PROTOCOL' => 'HTTP/1.1',
			'REMOTE_PORT' => '57213'
		);

		$server->httpRequest = new Request($env);
		$server->httpRequest->setBody($stream, true);

		$server->exec();

		$expected = 'HTTP/1.1 201 Created';
		$result = $server->httpResponse->status;
		$this->assertEqual($expected, $result);

		$file = File::first(array(
			'conditions' => array(
				'name' => $fileName . '.jpg'
			)
		));
		$this->assertTrue($file);

		$item = Item::first(array(
			'conditions' => array(
				'url' => $itemName
			)
		));
		$result = $item->alternate_images[0];
		$expected = (string) $file->_id;
		$this->assertEqual($expected, $result);

		fclose($stream);
		$item->delete();
		$event->delete();
		$file->delete();
	}
}

?>