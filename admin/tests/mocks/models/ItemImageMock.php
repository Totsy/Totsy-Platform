<?php

namespace admin\tests\mocks\models;

class ItemImageMock extends \admin\models\ItemImage {

	public static function resizeAndSave($position, $data, $meta = array()) {
		return static::create();
	}
}

?>