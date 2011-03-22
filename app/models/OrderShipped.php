<?php

namespace app\models;

class OrderShipped extends \lithium\data\Model {

	public static function collection() {
		return static::_connection()->connection->{"orders.shipped"};
	}

	protected $_meta = array('source' => 'orders.shipped');
}

?>