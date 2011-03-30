<?php

namespace admin\models;

class Banner extends \lithium\data\Model {

	public $validates = array();
	
	public static function collection() {
		return static::_connection()->connection->events;
	}
}

?>