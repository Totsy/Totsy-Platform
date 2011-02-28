<?php

namespace admin\models;

use MongoId;
use MongoDate;
use MongoRegex;
use lithium\data\Connections;
use lithium\util\Validator;

class Group extends \lithium\data\Model {

	public $validates = array(
		'newacl_route_0' => array(
			'correctRoute', 'required' => false,
			'message' => 'Please add a correct route (ex: "/orders/view/")'
		),
		'newacl_route_1' => array(
			'correctRoute', 'required' => false,
			'message' => 'Please add a correct route (ex: "/orders/view/")'
		),
		'newacl_route_2' => array(
			'correctRoute', 'required' => false,
			'message' => 'Please add a correct route (ex: "/orders/view/")'
		),
		'newacl_route_3' => array(
			'correctRoute', 'required' => false,
			'message' => 'Please add a correct route (ex: "/orders/view/")'
		),
		'newacl_connection_0' => array(
			'correctConnection', 'required' => false,
			'message' => 'Please add a correct connection (ex: "Orders::index")'
		),
		'newacl_connection_1' => array(
			'correctConnection', 'required' => false,
			'message' => 'Please add a correct connection (ex: "Orders::index")'
		),
		'newacl_connection_2' => array(
			'correctConnection', 'required' => false,
			'message' => 'Please add a correct connection (ex: "Orders::index")'
		),
		'newacl_connection_3' => array(
			'correctConnection', 'required' => false,
			'message' => 'Please add a correct connection (ex: "Orders::index")'
		)
	);

	public static function collection() {
		return static::_connection()->connection->groups;
	}

	public static function __init(array $options = array()) {
		parent::__init($options);

		Validator::add('correctRoute', function ($value) {
			$test = preg_match("#^/[a-zA-Z0-9_/]+/?(\{\:args\})?$#",$value);
			if($test || empty($value)){
				return true;
			} else {
				return false;
			}
		});

		Validator::add('correctConnection', function ($value) {
			$test = preg_match("#^[a-zA-Z_]+[:]{2,2}[a-zA-Z_]+$#",$value);
			if($test || empty($value)){
				return true;
			} else {
				return false;
			}
		});
	}
}

?>