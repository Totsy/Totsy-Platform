<?php

namespace admin\tests\integration\models;

use admin\models\Event;
use admin\models\File;
use li3_fixtures\test\Fixture;

class EventTest extends \lithium\test\Integration {

	public function testAttachDetachImage() {
		$fixtures = Fixture::load('Event');

		$fileA = File::write(uniqid());
		$fileB = File::write(uniqid());

		$event = Event::create($fixtures->first());
		$event->save();

		$event->attachImage('splash_big', $fileA->_id);
		$expected = array(
			'splash_big_image' => (string) $fileA->_id
		);
		$result = $event->images->data();
		$this->assertEqual($expected, $result);

		$event->attachImage('logo', $fileB->_id);
		$expected = array(
			'splash_big_image' => (string) $fileA->_id,
			'logo_image' => (string) $fileB->_id
		);
		$result = $event->images->data();
		$this->assertEqual($expected, $result);

		$event->detachImage('splash_big', $fileA->_id);
		$expected = array(
			'splash_big_image' => null,
			'logo_image' => (string) $fileB->_id
		);
		$event->save();
		$result = $event->images->data();
		$this->assertEqual($expected, $result);

		$event->delete();
		$fileA->delete();
		$fileB->delete();
	}

	public function testUpdateKeepsModifications() {
		$fixtures = Fixture::load('Event');

		$event = Event::create();
		$event->save($fixtures->first());

		$id = $event->_id;

		$event->images = array('foo' => 'bar');
		$event->save();

		$result = Event::first(array('conditions' => array('_id' => $id)))->data();
		$this->assertTrue(isset($result['modifications']));
		$this->assertFalse(isset($result['']['modifications']));

		$event->delete();
	}

	public function testMultipleUpdatesDoNotCorruptDocument() {
		$fixtures = Fixture::load('Event');

		$event = Event::create();
		$event->save($fixtures->first());

		$id = $event->_id;

		$event = Event::first(array('conditions' => array('_id' => $id)));
		$event->save();

		$event = Event::first(array('conditions' => array('_id' => $id)));
		$event->save();

		$event = Event::first(array('conditions' => array('_id' => $id)));

		$result = $event->data();
		$this->assertTrue(isset($result['modifications']));
		$this->assertFalse(isset($result['']['modifications']));

		$event->delete();
	}

	public function testAttachingFilesDoesNotCorruptDocument() {
		$fixtures = Fixture::load('Event');
		$fileA = File::write(uniqid());
		$fileB = File::write(uniqid());

		$event = Event::create();
		$event->save($fixtures->first());

		$id = $event->_id;

		$event = Event::first(array('conditions' => array('_id' => $id)));
		$event->attachImage('splash_big', $fileA->_id);
		$event->save();

		$result = Event::first(array('conditions' => array('_id' => $id)))->data();

		$event = Event::first(array('conditions' => array('_id' => $id)));
		$event->attachImage('event', $fileB->_id);
		$event->save();

		$event = Event::first(array('conditions' => array('_id' => $id)));

		$result = $event->data();
		$this->assertTrue(isset($result['modifications']));
		$this->assertFalse(isset($result['']['modifications']));

		$fileA->delete();
		$fileB->delete();
		$event->delete();
	}
}

?>