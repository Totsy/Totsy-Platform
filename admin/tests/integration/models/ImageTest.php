<?php

namespace admin\tests\integration\models;

use admin\models\Image;
use lithium\core\Libraries;

class ImageTest extends \lithium\test\Integration {

	protected $_backup = array();

	public function setUp() {
		$this->_backup['dedupe'] = Image::$dedupe;
		Image::$dedupe = false;
	}

	public function tearDown() {
		Image::$dedupe = $this->_backup['dedupe'];
	}

	public function testWriteAutoMeta() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = Image::write($bytes);

		$result = $file->dimensions->data();
		$expected = array('width' => 70, 'height' => 47);
		$this->assertEqual($expected, $result);

		$file->delete();
	}
	public function testResizeAndSaveWithBytes() {
		$backup = Image::$types;

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		Image::$types = array(
			'logo' => array(
				'dimensions' => array(3, 5)
			)
		);

		$file = Image::resizeAndSave('logo', $bytes);

		$result = $file->dimensions->data();
		$expected = array('width' => 3, 'height' => 5);
		$this->assertEqual($expected, $result);

		$file->delete();
		Image::$types = $backup;
	}

	public function testResizeAndSaveWithFileupload() {
		$backup = Image::$types;

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$path = Libraries::get('admin', 'resources');
		$uploaded = "{$path}/tmp/tests/uploaded.jpg";
		copy($file, $uploaded);

		Image::$types = array(
			'logo' => array(
				'dimensions' => array(3, 5)
			)
		);

		$data = array(
			'name' => 'image_jpg.jpg',
			'tmp_name' => $uploaded,
			'mime_type' => 'image/jpeg'
		);

		$file = Image::resizeAndSave('logo', $data);

		$result = $file->dimensions->data();
		$expected = array('width' => 3, 'height' => 5);
		$this->assertEqual($expected, $result);

		$result = $file->name;
		$expected = 'image_jpg.jpg';
		$this->assertEqual($expected, $result);

		$file->delete();
		unlink($uploaded);
		Image::$types = $backup;
	}

	public function testResizeAndSaveUpscaling() {
		$backup = Image::$types;

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		Image::$types = array(
			'logo' => array(
				'dimensions' => array(500, 300)
			)
		);

		$file = Image::resizeAndSave('logo', $bytes);

		$result = $file->dimensions->data();
		$expected = array('width' => 500, 'height' => 300);
		$this->assertEqual($expected, $result);

		$file->delete();
		Image::$types = $backup;
	}

	public function testDimensionsBc() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$id = Image::getGridFS()->storeBytes($bytes);
		$file = Image::first(array('conditions' => array('_id' => $id)));

		$result = $file->dimensions();
		$expected = array('width' => 70, 'height' => 47);
		$this->assertEqual($expected, $result);

		$file->delete();
	}
	public function testDetectDimensions() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$expected = array('width' => 70, 'height' => 47);
		$result = Image::detectDimensions($bytes);
		$this->assertEqual($expected, $result);
	}
}

?>