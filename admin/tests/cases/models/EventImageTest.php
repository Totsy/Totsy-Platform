<?php

namespace admin\tests\cases\models;

use admin\tests\mocks\models\EventImageMock;

class EventImageTest extends \lithium\test\Unit {

	public function testProcessMapEvent() {
		$names = array(
			'events_the-name.jpg',
			'events_the-name_image.jpg'
		);
		foreach ($names as $name) {
			$result = EventImageMock::process(uniqid(), compact('name'));
			$expected = 'event';
			$result = $result[0];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapLogo() {
		$names = array(
			'events_the-name_logo.jpg'
		);
		foreach ($names as $name) {
			$result = EventImageMock::process(uniqid(), compact('name'));
			$expected = 'logo';
			$result = $result[0];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapSmallSplash() {
		$names = array(
			'events_the-name_small_splash.jpg',
			'events_the-name_splash_small.jpg'
		);
		foreach ($names as $name) {
			$result = EventImageMock::process(uniqid(), compact('name'));
			$expected = 'splash_small';
			$result = $result[0];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapBigSplash() {
		$names = array(
			'events_the-name_big_splash.jpg',
			'events_the-name_splash_big.jpg'
		);
		foreach ($names as $name) {
			$result = EventImageMock::process(uniqid(), compact('name'));
			$expected = 'splash_big';
			$result = $result[0];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}
}

?>