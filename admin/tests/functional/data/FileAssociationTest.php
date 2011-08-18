<?php

namespace admin\tests\functional\data;

use admin\models\Event;
use admin\models\Item;
use admin\models\File;
use admin\extensions\command\FileOrphaned;

class FileAssociationTest extends \lithium\test\Integration {

	public function testAssociateWithEvent() {
		$file = File::write(uniqid());

		$event = Event::create(array(
			'title' => 'Test',
			'url' => $url = uniqid('test-'),
			'images' => array()
		));
		$event->save();

		$result = $event->attachImage('logo', $file->_id)->save();
		$this->assertTrue($result);

		$expected = array('logo_image' => (string) $file->_id);
		$result = Event::first(array('conditions' => array('_id' => $event->_id)))->images->data();
		$this->assertEqual($expected, $result);

		$event->delete();
		$file->delete();
	}

	public function testAssociateWithItem() {
		$file = File::write(uniqid());

		$item = Item::create(array(
			'title' => 'Test',
			'url' => $url = uniqid('test-')
		));
		$item->save();

		$result = $item->attachImage('zoom', $file->_id)->save();
		$this->assertTrue($result);

		$expected = (string) $file->_id;
		$result = Item::first(array('conditions' => array('_id' => $item->_id)))->zoom_image;
		$this->assertEqual($expected, $result);

		$item->delete();
		$file->delete();
	}

	public function testUsed() {
		$file = File::write(uniqid());

		$result = File::used($file->_id);
		$this->assertFalse($result);

		$event = Event::create(array(
			'title' => 'Test',
			'url' => $url = uniqid('test-'),
			'images' => array(
				'logo_image' => $file->_id
			)
		));
		$event->save();

		$result = File::used($file->_id);
		$this->assertTrue($result);

		$event->delete();

		$result = File::used($file->_id);
		$this->assertFalse($result);

		$file->delete();
	}

	public function testOrphaned() {
		$before = count(File::orphaned());

		$file = File::write(uniqid());
		$event = Event::create(array(
			'title' => 'Test',
			'url' => $url = uniqid('test-'),
			'images' => array(
				'logo_image' => $file->_id
			)
		));
		$event->save();

		$expected = 0;
		$result = count(File::orphaned()) - $before;
		$this->assertIdentical($expected, $result);

		$event->delete();

		$expected = 0;
		$result = count(File::orphaned()) - $before;
		$this->assertIdentical($expected, $result);

		$command = new FileOrphaned();
		$command->run();

		$expected = 1;
		$result = count(File::orphaned()) - $before;
		$this->assertIdentical($expected, $result);

		$file->delete();
	}

	public function testAssociatedFilesCannotBeDeleted() {
		$file = File::write(uniqid());

		$event = Event::create(array(
			'title' => 'Test',
			'url' => $url = uniqid('test-'),
			'images' => array(
				'logo_image' => $file->_id
			)
		));
		$event->save();

		$result = $file->delete();
		$this->assertFalse($result);

		$event->delete();

		$result = $file->delete();
		$this->assertTrue($result);
	}
}

?>