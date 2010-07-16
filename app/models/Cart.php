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
		return static::_object()->save($data);
	}
	
	public static function active($params = null, array $options = array()) {
		$fields = $params['fields'];
		return static::all(array(
			'conditions' => array(
				'session' => Session::key()),
			'fields' => $fields,
			'order' => array('expires' => 'ASC') 
		));
	}
	
	public function itemCount() {
		$cart = Cart::active(array(
			'fields' => array('quantity')
		));
		$cartCount = 0;
		if (!empty($cart)) {
			foreach ($cart as $item) {
				$cartCount += $item->quantity;
			}
		}
		return $cartCount;
	}

}

?>