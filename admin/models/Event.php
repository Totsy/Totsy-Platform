<?php

namespace admin\models;
use MongoId;

class Event extends \lithium\data\Model {

	public $validates = array();

	public static $tags = array(
		'holiday' => 'holiday',
		'special' => 'special',
		'toys' => 'toys'
	);

	public static function collection() {
		return static::_connection()->connection->events;
	}
	
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

	public static function removeItems($event) {
		return static::collection()->update(
			array('_id' => new MongoId($event)),
			array('$unset' => array('items' => true)
		));
	}
}

?>