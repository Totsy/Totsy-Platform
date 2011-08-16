<?php

namespace admin\tests\mocks\models;

class ImageMock extends \admin\models\Image {

	public static function resizeAndSave($position, $data, $meta = array()) {
		return static::create();
	}
}

?>