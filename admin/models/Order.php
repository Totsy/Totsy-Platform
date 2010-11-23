<?php

namespace admin\models;

use MongoId;
use MongoDate;
use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\exceptions\TransactionException;
use MongoRegex;

/**
* The Orders Model is related to the Orders Collection in MongoDB.
*
* This model is touched by both the admin and app. The main purpose of this class is
* to process the orders with authorize.net and set information to the order during
* processing.
*
* Here is an example of a real schema:
*
* {{{
*	{
*		"_id" : ObjectId("4c605e12ce64e5df2c1c2100"),
*		"authKey" : "111111111",
*		"auth_confirmation" : "111111111",
*		"billing" : {},
*		"card_number" : "xxxx",
*		"date_created" : "Mon Aug 09 2010 15:59:11 GMT-0400 (EDT)",
*		"giftMessage" : "",
*		"handling" : 8.68,
*		"items" : [
*			{See cart}
*		]
*		"order_id" : "4c605e12",
*		"payment_date" : "Thu Sep 02 2010 16:12:34 GMT-0400 (EDT)",
*		"shipping" : {}
*		"shippingMethod" : "ups",
*		"subTotal" : 33.9,
*		"tax" : 0,
*		"total" : 42.58,
*		"tracking_numbers" : [
*			"'1ZY8Y293030xxxxxxxx"
*		],
*		"user_id" : "4c5f88427bc2e06b5c44bbae"
* }}}
**/
class Order extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0
	);

	public static function collection() {
		return static::_connection()->connection->orders;
	}
	public $validates = array(
		'authKey' => 'Could not secure payment.'
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}
	
	public static function lookup($orderId) {
		$orderId = new MongoRegex("/$orderId/i");
		$result = static::find('first', array('conditions' => array(
			'order_id' => $orderId
		)));
		return $result;
	}

	public function process($order) {
		try {
			return $order->save(array(
				'payment_date' => static::dates('now'),
				'auth_confirmation' => Payments::capture('default', $order->authKey, round($order->total, 2)),
				'auth_error' => null
			));
		} catch (TransactionException $e) {
			$order->errors($order->errors() + array($e->getMessage()));
			$order->auth_error = array($e->getMessage());
			$order->auth_confirmation = -1;
			$order->save();
		}
	}

	public static function setTrackingNumber($order_id, $number) {
		$set = array('$addToSet' => array('tracking_numbers' => $number));
		return static::collection()->update(array('order_id' => $order_id), $set);
	}

	public static function findUserOrder($data) {
		$type = strtolower($data['address_type']);
		$exclude = array('address_type', 'type');
		foreach ($data as $key => $value) {
			if (($value != '') && (!in_array($key, $exclude))) {
				$conditions["$type.$key"] = new MongoRegex("/$value/i");
			}
		}
		return static::find('all', array('conditions' => $conditions));
	}

}

?>