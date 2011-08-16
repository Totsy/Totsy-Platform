<?php

namespace admin\tests\mocks\models;

class ItemMock extends \admin\models\Item {

	public static function updateImage($name, $id, $conditions = array()) {
		return func_get_args();
	}
}

?>