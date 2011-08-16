<?php

namespace admin\tests\mocks\models;

class EventImageMock extends \admin\models\EventImage {

	public static function resizeAndSave($position, $data, $meta = array()) {
		return static::create();
	}
}

?>