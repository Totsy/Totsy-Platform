<?php

namespace admin\tests\integration\models;

use admin\models\Item;
use admin\models\File;
use li3_fixtures\test\Fixture;

class ItemTest extends \lithium\test\Integration {

	protected $_backup = array();

	public function setUp() {
		$this->_backup['dedupe'] = File::$dedupe;
		File::$dedupe = false;
	}

	public function tearDown() {
		File::$dedupe = $this->_backup['dedupe'];
	}
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

	public function testAttachMultipleImage() {
		$fixtures = Fixture::load('Item');

		$file = File::write(uniqid());

		$item = Item::create($fixtures->first());
		$item->save();

		$item->attachImage('alternate', $file->_id);

		/* Disabled as a work around for saving nested documents is in place. */
		/*
		$expected = array((string) $file->_id);
		$result = $item->alternate_images->data();
		$this->assertEqual($expected, $result);
		*/

		$item = Item::first(array('conditions' => array('_id' => (string) $item->_id)));
		$expected = array((string) $file->_id);
		$result = $item->alternate_images->data();
		$this->assertEqual($expected, $result);

		$item->delete();
		$file->delete();
	}
	public function testAttachImageAndSave() {
		$fixtures = Fixture::load('Item');

		$file = File::write(uniqid());

		$item = Item::create($fixtures->first());
		$item->save();

		$item->attachImage('primary', $file->_id);
		$expected = (string) $file->_id;
		$result = $item->primary_image;
		$this->assertEqual($expected, $result);

		$item->save();

		$item = Item::first(array('conditions' => array('_id' => $item->_id)));
		$expected = (string) $file->_id;
		$result = $item->primary_image;
		$this->assertEqual($expected, $result);

		$item->delete();
		$file->delete();
	}
}

?>