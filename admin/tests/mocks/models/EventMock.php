<?php

namespace admin\tests\mocks\models;

use lithium\data\entity\Document;

class EventMock extends \admin\models\Event {

	public static $attachImageArgs = array();
	public static $detachImageArgs = array();

	public static function find($type, array $options = array()) {
		return static::create();
	}

	public function save($entity, $data = null, array $options = array()) {
		return true;
	}

	public function attachImage($entity, $name, $id) {
		static::$attachImageArgs = func_get_args();
		return parent::attachImage($entity, $name, $id);
	}

	public function detachImage($entity, $name, $id) {
		static::$detachImageArgs = func_get_args();
		return parent::detachImage($entity, $name, $id);
	}
}

?>