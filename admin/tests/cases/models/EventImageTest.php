<?php

namespace admin\tests\cases\models;
use admin\tests\mocks\models\EventMock;
use admin\tests\mocks\models\EventImageMock;

class EventImageTest extends \lithium\test\Unit {

	public function testProcessMapEvent() {
		$names = array(
			'events_the-name.jpg',
			'events_the-name_image.jpg'
		);
		foreach ($names as $name) {
			EventImageMock::process(uniqid(), compact('name'));

			$expected = 'event';
			$result = EventMock::$attachImageArgs[1];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapLogo() {
		$names = array(
			'events_the-name_logo.jpg'
		);
		foreach ($names as $name) {
			EventImageMock::process(uniqid(), compact('name'));

			$expected = 'logo';
			$result = EventMock::$attachImageArgs[1];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapSmallSplash() {
		$names = array(
			'events_the-name_small_splash.jpg',
			'events_the-name_splash_small.jpg'
		);
		foreach ($names as $name) {
			EventImageMock::process(uniqid(), compact('name'));

			$expected = 'splash_small';
			$result = EventMock::$attachImageArgs[1];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testProcessMapBigSplash() {
		$names = array(
			'events_the-name_big_splash.jpg',
			'events_the-name_splash_big.jpg',
			'events_test_splash_big.jpg',
		);
		foreach ($names as $name) {
			EventImageMock::process(uniqid(), compact('name'));

			$expected = 'splash_big';
			$result = EventMock::$attachImageArgs[1];
			$this->assertEqual($expected, $result, "Name `{$name}` wasn't mapped to `{$expected}`.");
		}
	}

	public function testExtractUrl() {
		$names = array(
			'events_the-name.jpg' => 'the-name',
			'events_the-name_image.jpg' => 'the-name',
			'events_the-name_big_splash.jpg' => 'the-name',
			'events_the-name_splash_big.jpg' => 'the-name',
			'events_test_splash_big.jpg' => 'test'
		);
		foreach ($names as $name => $url) {
			$expected = $url;
			$result = EventImageMock::extractUrl($name);
			$message = "URL `{$url}` wasn't extracted from `{$name}`, but `{$result}`.";
			$this->assertEqual($expected, $result, $message);
		}
	}
}

?>