<?php

namespace admin\tests\cases\models;

use admin\tests\mocks\models\ItemMock;
use admin\tests\mocks\models\ItemImageMock;

class ItemImageTest extends \lithium\test\Unit {

	public function testProcessMapPrimary() {
		$names = array(
			'items_shirt_primary.jpg',
			'items_shirt-yellow_primary.jpg'
		);
		foreach ($names as $name) {
			$result = ItemImageMock::process(uniqid(), compact('name'));
			$expected = 'primary';
			$result = ItemMock::$attachImageArgs[1];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapZoom() {
		$names = array(
			'items_shirt_zoom.jpg',
			'items_shirt-yellow_zoom.jpg'
		);
		foreach ($names as $name) {
			$result = ItemImageMock::process(uniqid(), compact('name'));
			$expected = 'zoom';
			$result = ItemMock::$attachImageArgs[1];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapAlternate() {
		$names = array(
			'items_shirt_alternate.jpg',
			'items_shirt-blue_alternate.jpg',
			'items_shirt-blue_alternate0.jpg',
			'items_shirt-blue_alternateB.jpg',
		);
		foreach ($names as $name) {
			$result = ItemImageMock::process(uniqid(), compact('name'));
			$expected = 'alternate';
			$result = ItemMock::$attachImageArgs[1];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}
}

?>