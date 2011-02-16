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
*   }
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
		'authKey' => 'Could not secure payment.',
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

	public static function process($order) {
		try {
			$auth = Payments::capture('default', $order['authKey'], round($order['total'], 2));
			$collection = static::_object()->collection();
			$orderId = new MongoId($order['_id']);
			return $collection->update(
				array('_id' => $orderId),
				array('$set' => array(
					'payment_date' => new MongoDate(),
					'auth_confirmation' => $auth,
					'auth_error' => null)),
				array('upsert' => false)
			);
		} catch (TransactionException $e) {
			$collection->update(
				array('_id' => $orderId),
				array('$set' => array(
					'error_date' => new MongoDate(),
					'auth_confirmation' => -1,
					'auth_error' => $e->getMessage())),
				array('upsert' => false)
			);
		}
	}

	public static function setTrackingNumber($order_id, $number) {
		$set = array('$addToSet' => array('tracking_numbers' => $number));
		return static::collection()->update(array('order_id' => $order_id), $set);
	}

	/**
	 * Search for an order by either name or address.
	 * We are limiting this order search by the official launch of August 3rd.
	 * There are way too many issues when dealing with orders that are from the old system.
	 * @param string $data,
	 * @param string $type
	 * @return array
	 */
	public static function orderSearch($data, $type) {
		$keys = array(
			'name' => array(
				'shipping.firstname',
				'shipping.lastname',
				'billing.firstname',
				'billing.lastname'),
			'address' => array(
				'shipping.address',
				'billing.address')
		);
		foreach ($keys[$type] as $key) {
			$conditions[] = array($key => new MongoRegex("/$data/i"));
		}
		$orders = static::collection();
		$date = array('date_created' => array('$gt' => new MongoDate(strtotime('August 3, 2010'))));
		return $orders->find(array('$or' => $conditions) + $date)->sort(array('date_created' => 1));
	}

	/**
	 * Cancel an order.
	 * By putting the "cancel" field on the db to true if the order is uncanceled.
	 * Uncancel an order.
	 * By putting the "cancel" field on the db to false if the order is canceled..
	 * And to find the author of this modification, we add a "cancel_by" field.
	 * @param string $order_id,
	 * @param string $author
	 */
	public static function cancel($order_id, $author) {
		//Get the actual datas of the order
		$result = static::find('first', array('conditions' => array(
			'_id' => $order_id
		)));
		$order = $result->data();
		//Compare the cancel status, write modification datas and update cancel db status
		$modification_datas["author"] = $author;
		$modification_datas["date"] = new MongoDate(strtotime('now'));
		if(empty($order["cancel"]) || ($order["cancel"] == false)){
			$modification_datas["type"] = "cancel";
			static::collection()->update(array('_id' => new MongoId($order_id)),
				array('$set' => array('cancel' => true)), array("upsert" => true));
		}else{
			$modification_datas["type"] = "uncancel";
			static::collection()->update(array('_id' => new MongoId($order_id)),
				array('$set' => array('cancel' => false)), array("upsert" => true));
		}
		//Pushing modification datas to db
		static::collection()->update(array("_id" => new MongoId($order_id)),
		array('$push' => array('modifications' => $modification_datas)), array('upsert' => true));
	}
}

?>