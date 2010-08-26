<?php

namespace admin\models;

use MongoId;
use MongoDate;
use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\exceptions\TransactionException;
use \MongoRegex;

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
		$result = static::find('first', array('conditions' => array('order_id' => $orderId)));
		return $result;
	}

	public function process($order) {
		try {
			return $order->save(array(
				'date_payment' => static::dates('now'),
				'processKey' => Payments::capture('default', $order->total, $order->authKey)
			));
		} catch (TransactionException $e) {
			$order->set($data);
			$order->errors($order->errors() + array($e->getMessage()));
		}
	}

}

?>