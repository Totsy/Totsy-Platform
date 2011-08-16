<?php

namespace admin\tests\integration\models;

use admin\models\File;

class FileTest extends \lithium\test\Integration {

	protected $_backup = array();

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
		$file = File::write(uniqid(), array('foo' => 'bar'));

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
		$bytes = uniqid();

		$file = File::write($bytes);

		$resultA = $file->_id;
		$resultB = File::write($bytes)->_id;
		$this->assertEqual($resultA, $resultB);

		$file->delete();
	}

	public function testWriteDoesNotUpdateDupeMeta() {
		$bytes = uniqid();

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

	public function testPendingWithFlaggedFalse() {
		$before = File::pending()->count();

		$fileA = File::write('test-a', array('pending' => true));
		$fileB = File::write('test-b', array('pending' => false));

		$result = File::pending()->count() - $before;
		$expected = 1;
		$this->assertEqual($expected, $result);

		$fileA->delete();
		$fileB->delete();
	}
	public function testDetectMimeTypeFromBytes() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$expected = 'image/jpeg';
		$result = File::detectMimeType($bytes);
		$this->assertEqual($expected, $result);

		$file = LITHIUM_APP_PATH . '/tests/data/image_png.png';
		$bytes = file_get_contents($file);

		$expected = 'image/png';
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

		$file = LITHIUM_APP_PATH . '/tests/data/image_png.png';
		$handle = fopen($file, 'rb');

		$expected = 'image/png';
		$result = File::detectMimeType($handle);
		$this->assertEqual($expected, $result);

		fclose($handle);
	}

	public function testDetectMimeTypeFail() {
		$result = File::detectMimeType('');
		$this->assertFalse($result);
	}

	public function testExtension() {
		$this->_backup['dedupe'] = File::$dedupe;
		File::$dedupe = false;

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);
		$file = File::write($bytes);

		$expected = 'jpg';
		$result = $file->extension();
		$this->assertEqual($expected, $result);

		$file->delete();

		$file = LITHIUM_APP_PATH . '/tests/data/image_png.png';
		$bytes = file_get_contents($file);
		$file = File::write($bytes);

		$expected = 'png';
		$result = $file->extension();
		$this->assertEqual($expected, $result);

		$file->delete();

		$bytes = 'This is some text.';
		$file = File::write($bytes);

		$expected = 'txt';
		$result = $file->extension();
		$this->assertEqual($expected, $result);

		$file->delete();

		File::$dedupe = $this->_backup['dedupe'];
	}

	public function testExtensionWithNameHinting() {
		$this->_backup['dedupe'] = File::$dedupe;
		File::$dedupe = false;

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);
		$file = File::write($bytes, array('name' => 'image.jpg'));

		$expected = 'jpg';
		$result = $file->extension();
		$this->assertEqual($expected, $result);

		$file->delete();

		$file = LITHIUM_APP_PATH . '/tests/data/image_png.png';
		$bytes = file_get_contents($file);
		$file = File::write($bytes, array('name' => 'image.png'));

		$expected = 'png';
		$result = $file->extension();
		$this->assertEqual($expected, $result);

		$file->delete();

		$bytes = 'This is some text.';
		$file = File::write($bytes, array('name' => 'image.txt'));

		$expected = 'txt';
		$result = $file->extension();
		$this->assertEqual($expected, $result);

		$file->delete();

		$bytes = 'This is some text.';
		$file = File::write($bytes, array('name' => 'image.TXT'));

		$expected = 'txt';
		$result = $file->extension();
		$this->assertEqual($expected, $result);

		$file->delete();

		File::$dedupe = $this->_backup['dedupe'];
	}
	public function testDimensionsBc() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$id = File::getGridFS()->storeBytes($bytes);
		$file = File::first(array('conditions' => array('_id' => $id)));

		$result = $file->mimeType();
		$expected = 'image/jpeg';
		$this->assertEqual($expected, $result);

		$file->delete();
	}
}

?>