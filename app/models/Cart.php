<?php

namespace app\models;

use \lithium\storage\Session;
use \MongoDate;

class Cart extends \lithium\data\Model {

	public $validates = array();
	
	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);
	
	public static function dates($name) { 
	     return new MongoDate(time() + static::_object()->_dates[$name]); 
	}
	
	public static function addFields($data, array $options = array()) {

		$data->expires = static::dates('tenMinutes');
		$data->created = static::dates('now');
		$data->session = Session::key();
		$user = Session::read('userLogin');
		$data->user = $user['_id'];
		return static::save($data);
	}

}

?>