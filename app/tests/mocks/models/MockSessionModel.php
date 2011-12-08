<?php

namespace app\tests\mocks\models;

use app\tests\mocks\models\MockSessionEntity;

class MockSessionModel extends \lithium\core\StaticObject {
	public static $log = array();
	protected static $_fakeFind = false;

	public static function key($values = array()) {
		static::_log('key', array($values));
		$key = "_id";
		return !$values ? $key : array($key => $values);
	}

	public static function create(array $data = array(), array $options = array()) {
		static::_log('create', array($data, $options));
		return new MockSessionEntity(compact('data'));
	}

	public static function find($type, array $options = array()) {
		static::_log('find', array($type, $options));
		if (!static::$_fakeFind) {
			return null;
		}
		$data = array('faked' => 'faked');
		return new MockSessionEntity(compact('data'));
	}

	public static function __callStatic($method, $params) {
		static::_log($method, $params);
		return null;
	}

	public static function fakeFind() {
		static::$_fakeFind = true;
	}

	public static function unfakeFind() {
		static::$_fakeFind = false;
	}

	public static function resetLog() {
		static::$log = array();
	}

	protected static function _log($method, $params) {
		static::$log[] = array($method, $params);
	}
}

?>