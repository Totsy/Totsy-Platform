<?php

namespace admin\models;

use MongoId;
use MongoDate;
use MongoRegex;
use lithium\data\Connections;

class Group extends \lithium\data\Model {

	public $validates = array();

	public static function collection() {
		return static::_connection()->connection->groups;
	}
}

?>