<?php

namespace admin\models;

class Event extends \lithium\data\Model {

	public $validates = array();
	
	protected $_booleans = array(
		'enabled'
		);
		
	public static function castData($event, array $options = array()) {
		
		foreach ($event as $key => $value) {
			if (in_array($key, static::_object()->_booleans)) {
				$event[$key] = (boolean) $value;
			}
		}
		return $event;
	}
}

?>