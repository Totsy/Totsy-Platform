<?php

namespace admin\tests\functional\

use lithium\action\Request;
use admin\models\Order;
use MongoId;

class Order extends \lithium\test\Unit {
	/*
	* Run Auth.Net Process For One Order
	*/
	public function updateOrder() {
		$orderCollection = Order::collection();
		//configuration
		$order_id = "";
		//Update authorize.net Total
		$order = $orderCollection->findOne(array("_id" => new MongoId($order_id)));
		Order::process($order);
	}
}