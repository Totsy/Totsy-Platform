<?php

namespace app\models;

use \MongoDate;

class Promotion extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]); 
	}
}

?>