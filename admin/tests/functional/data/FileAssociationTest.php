<?php

namespace admin\tests\functional\data;

use admin\models\Event;
use admin\models\Item;
use admin\models\File;

class FileAssociationTest extends \lithium\test\Integration {

	public function testAssociateWithEvent() {
		$file = File::write(uniqid());

		$event = Event::create(array(
			'title' => 'Test',
			'url' => $url = uniqid('test-'),
			'images' => array()
		));
		$event->save();

		$result = Event::updateImage('logo', $file->_id, compact('url'));
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

		$result = Item::updateImage('zoom', $file->_id, compact('url'));
		$this->assertTrue($result);

		$expected = (string) $file->_id;
		$result = Item::first(array('conditions' => array('_id' => $item->_id)))->zoom_image;
		$this->assertEqual($expected, $result);

		$item->delete();
		$file->delete();
	}
}

?>