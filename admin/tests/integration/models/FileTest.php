<?php

namespace admin\tests\integration\models;

use admin\models\File;

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

	public function testWriteMeta() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = File::write($bytes, array('foo' => 'bar'));

		$result = $file->foo;
		$expected = 'bar';
		$this->assertEqual($expected, $result);

		$file->delete();
	}

	public function testWriteAutoMeta() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = File::write($bytes);

		$result = $file->mime_type;
		$expected = 'image/jpeg';
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

	public function testPending() {
		$before = File::pending()->count();

		$fileA = File::write('test-a');
		$fileB = File::write('test-b', array('pending' => true));
		$fileC = File::write('test-c', array('pending' => true));

		$result = File::pending()->count() - $before;
		$expected = 2;
		$this->assertEqual($expected, $result);

		$fileA->delete();
		$fileB->delete();
		$fileC->delete();
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
}

?>