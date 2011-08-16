<?php

namespace admin\tests\mocks\models;

class EventMock extends \admin\models\Event {

	public static function updateImage($name, $id, $conditions = array()) {
		return func_get_args();
	}
}

?>