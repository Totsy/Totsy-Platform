<?php

namespace admin\models;
use MongoDate;

class Base extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0,
		'-1min' => -60,
		'-3min' => -180,
		'-5min' => -300,
		'3min' => 180,
		'5min' => 300,
		'15min' => 900
	);
	
	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

}

?>