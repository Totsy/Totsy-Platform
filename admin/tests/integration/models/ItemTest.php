<?php

namespace admin\tests\integration\models;

use admin\models\Item;
use admin\models\File;
use li3_fixtures\test\Fixture;

class ItemTest extends \lithium\test\Integration {

	public function testAttachDetachImage() {
		$fixtures = Fixture::load('Item');

		$fileA = File::write(uniqid());
		$fileB = File::write(uniqid());

		$item = Item::create($fixtures->first());
		$item->save();

		$item->attachImage('primary', $fileA->_id);
		$expected = (string) $fileA->_id;
		$result = $item->primary_image;
		$this->assertEqual($expected, $result);

		$item->attachImage('zoom', $fileB->_id);
		$expected = (string) $fileA->_id;
		$result = $item->primary_image;
		$this->assertEqual($expected, $result);
		$expected = (string) $fileB->_id;
		$result = $item->zoom_image;
		$this->assertEqual($expected, $result);

		$item->detachImage('primary', $fileA->_id);
		$result = $item->primary_image;
		$this->assertNull($result);
		$expected = (string) $fileB->_id;
		$result = $item->zoom_image;
		$this->assertEqual($expected, $result);

		$item->delete();
		$fileA->delete();
		$fileB->delete();
	}
}

?>