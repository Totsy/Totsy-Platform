<?php

namespace admin\tests\integration\models;

use admin\models\File;
use li3_fixtures\test\Fixture;

class FileTest extends \lithium\test\Integration {

	public function testWriteUsingBytes() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = File::write($bytes);

		$result = $file;
		$this->assertTrue($result);

		$result = $file->file->getBytes();
		$expected = $bytes;
		$this->assertEqual($expected, $result);

		$file->delete();
	}

	public function testWriteUsingStream() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$handle = fopen($file, 'rb');

		$file = File::write($handle);

		$result = $file;
		$this->assertTrue($result);

		rewind($handle);

		$result = $file->file->getBytes();
		$expected = stream_get_contents($handle);
		$this->assertEqual($expected, $result);

		$file->delete();
		fclose($handle);
	}

	public function testWriteAutoMeta() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = File::write($bytes);

		$result = $file->mime_type;
		$expected = 'image/jpeg';
		$this->assertEqual($expected, $result);

		$result = $file->dimensions->data();
		$expected = array('width' => 70, 'height' => 47);
		$this->assertEqual($expected, $result);

		$result = $file->created_date->sec;
		$this->assertTrue(is_integer($result));

		$file->delete();
	}

	public function testWriteDoesDeduping() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = File::write($bytes);

		$resultA = $file->_id;
		$resultB = File::write($bytes)->_id;
		$this->assertEqual($resultA, $resultB);

		$file->delete();
	}

	public function testWriteDoesNotUpdateDupeMeta() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = File::write($bytes, array('name' => 'a.jpg'));

		$result = File::write($bytes, array('name' => 'b.jpg'))->name;
		$expected = 'a.jpg';
		$this->assertEqual($expected, $result);

		$file->delete();
	}

	public function testDetectMimeTypeFromBytes() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$expected = 'image/jpeg';
		$result = File::detectMimeType($bytes);
		$this->assertEqual($expected, $result);
	}

	public function testDetectMimeTypeFromStream() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$handle = fopen($file, 'rb');

		$expected = 'image/jpeg';
		$result = File::detectMimeType($handle);
		$this->assertEqual($expected, $result);

		fseek($handle, 10);
		$expected = 'image/jpeg';
		$result = File::detectMimeType($handle);
		$this->assertEqual($expected, $result);

		fclose($handle);
	}

	public function testDetectMimeTypeFail() {
		$result = File::detectMimeType('');
		$this->assertFalse($result);
	}

	public function testDetectDimensions() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$expected = array('width' => 70, 'height' => 47);
		$result = File::detectDimensions($bytes);
		$this->assertEqual($expected, $result);
	}
}

?>