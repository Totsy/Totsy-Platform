<?php

namespace admin\tests\integration\models;

use admin\models\Image;
use li3_fixtures\test\Fixture;

class ImageTest extends \lithium\test\Integration {

	public function testWriteAutoMeta() {
		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);

		$file = Image::write($bytes, array(), array('dedupe' => false));

		$result = $file->dimensions->data();
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