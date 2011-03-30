<?php

namespace admin\models;

use MongoId;
use MongoDate;
use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\exceptions\TransactionException;
use MongoRegex;
use lithium\analysis\Logger;
use admin\models\User;
use admin\models\Item;

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

	const TAX_RATE = 0.08875;

	const TAX_RATE_NYS = 0.04375;

	protected $_nyczips = array(
		'100',
		'104',
		'111',
		'114',
		'116',
		'11004',
		'11005'
	);

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

	/**
	 * Voids and Order
	 *
	 * @param array $order - Array of order information
	 * @return boolean
	 */
	public static function void($order) {
		$collection = static::collection();
		$orderId = new MongoId($order['_id']);
		try {
			$auth = Payments::void('default', $order['authKey']);
			return $collection->update(
				array('_id' => $orderId),
				array('$set' => array(
					'void_date' => new MongoDate(),
					'void_confirm' => $auth)),
				array('upsert' => false)
			);
		} catch (TransactionException $e) {
			$error = $e->getMessage();
			Logger::error("order-void: Void Failed. Error $error thrown for $order[_id]");
			$collection->update(
				array('_id' => $orderId),
				array('$set' => array(
					'error_date' => new MongoDate(),
					'auth_error' => $error)),
				array('upsert' => false)
			);
		}
	}
	public static function process($order) {
		$collection = static::collection();
		$orderId = new MongoId($order['_id']);
		try {
			$auth = Payments::capture('default', $order['authKey'], round($order['total'], 2));
			Logger::info("process-payment: Processed payment for order_id $order[_id]");
			return $collection->update(
				array('_id' => $orderId),
				array('$set' => array(
					'payment_date' => new MongoDate(),
					'auth_confirmation' => $auth,
					'auth_error' => null)),
				array('upsert' => false)
			);
		} catch (TransactionException $e) {
			$error = $e->getMessage();
			Logger::info("process-payment: Failed to process payment for order_id $order[_id]");
			Logger::error("process-payment: Error $error thrown for $order[_id]");
			$collection->update(
				array('_id' => $orderId),
				array('$set' => array(
					'error_date' => new MongoDate(),
					'auth_confirmation' => -1,
					'auth_error' => $error)),
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
	 * @param string $comment
	 */
	public static function cancel($order_id, $author, $comment) {
		$userCollection = User::collection();
		//Get the actual datas of the order
		$result = static::find('first', array('conditions' => array(
			'_id' => new MongoId($order_id)
		)));
		$order = $result->data();
		if(strlen($order["user_id"]) > 10){
			$user = $userCollection->findOne(array("_id" => new MongoId($order["user_id"])));
		} else {
			$user = $userCollection->findOne(array("_id" => $order["user_id"]));
		}
		//Compare the cancel status, write modification datas and update cancel db status
		$modification_datas["author"] = $author;
		$modification_datas["date"] = new MongoDate(strtotime('now'));
		if(empty($order["cancel"]) || ($order["cancel"] == false)){
			$modification_datas["type"] = "cancel";
			$modification_datas["comment"] = $comment;
			static::collection()->update(array('_id' => new MongoId($order_id)),
				array('$set' => array('cancel' => true)), array("upsert" => true));
			//Authorize.Net Void
			static::void($order);
			//Cancel all the items
			$items = $order["items"];
			foreach($order["items"] as $key => $item){
				$items[$key]["cancel"] = true;
			}
			static::collection()->update(array('_id' => new MongoId($order_id)),
				array('$set' => array('items' => $items)));
		}
		//Reattribute credits to the user
		if(isset($order["credit_used"])) {
			if(strlen($order["user_id"]) > 10){
				$userCollection->update(array("_id" => new MongoId($order["user_id"])), array('$set' => array("total_credit" => (((float) abs($order["credit_used"])) + ((float) $user["total_credit"])))));
			} else {
				$userCollection->update(array("_id" => $order["user_id"]), array('$set' => array("total_credit" => (((float) abs($order["credit_used"])) + ((float) $user["total_credit"])))));
			}
		}
		//Pushing modification datas to db
		$result = static::collection()->update(array("_id" => new MongoId($order_id)),
		array('$push' => array('modifications' => $modification_datas)), array('upsert' => true));
		return $result;
	}

	public static function shipping($items) {
		$cost = 7.95;
		$orderCheck = $items;
		if (count($orderCheck) == 1 && Item::first($orderCheck[0]['item_id'])->shipping_exempt && ((Item::first($orderCheck[0]['item_id'])->cancel == false) || empty(Item::first($orderCheck[0]['item_id'])->cancel)) ) {
			$cost = 0;
		}
		if (count($orderCheck) == 1 && !Item::first($orderCheck[0]['item_id'])->shipping_exempt && Item::first($orderCheck[0]['item_id'])->shipping_oversize && ((Item::first($orderCheck[0]['item_id'])->cancel == false) || empty(Item::first($orderCheck[0]['item_id'])->cancel))) {
			$cost = 0;
		}
		return $cost;
	}

	public static function overSizeShipping($items){
		$itemsCollection = Item::collection();
		$cost = 0;
		foreach($items as $item) {
			$info = $itemsCollection->findOne(array("_id" => new MongoId($item['item_id'])));
			if(array_key_exists('shipping_oversize', $info)){
				if(empty($info["cancel"]) || ($info["cancel"] == false)){
					$cost += $info['shipping_rate'];
				}
			}
		}
		return $cost;
	}

	/**
	 * Computes the sales tax for all order items, based on the shipping destination.
	 *
	 * @param object $current_order
	 * @param array $items
	 */
	public static function tax($current_order,$items) {
		$orderCollection = static::collection();
		$order = $orderCollection->findOne(array("_id" => new MongoId($current_order["id"])), array('shipping' => 1));
		$shipping = $order["shipping"];
		$tax = 0;
		foreach($items as $item){
			if(($item["cancel"] == false) || (empty($item["cancel"]))){
				$zipCheckPartial = in_array(substr($shipping["zip"], 0, 3), static::_object()->_nyczips);
				$zipCheckFull = in_array($shipping["zip"], static::_object()->_nyczips);
				$nysZip = ($zipCheckPartial || $zipCheckFull) ? true : false;
				$nycExempt = ($nysZip && $item->sale_retail < 110) ? true : false;
				if (!empty($item['taxable']) || $nycExempt) {
					switch ($shipping["state"]) {
						case 'NY':
							$tax = ($nysZip) ? static::TAX_RATE : static::TAX_RATE_NYS;
							break;
						default:
							$tax =  ($item['sale_retail'] < 110) ? 0 : static::TAX_RATE;
							break;
					}
				}
				if(!empty($item['tax'])){
					$tax += ($item['sale_retail'] * $item['tax']);
				}
			}
		}
		return $tax;
	}

	/**
	* Computes the subtotal of all order items when it's not canceled
	* @param array $items
	* @return float $subtotal
	*/
	public static function subTotal($items) {
		$subtotal = 0;
		foreach($items as $item){
			if(($item["cancel"] == false) || (empty($item["cancel"]))){
				$subtotal += ($item['sale_retail'] * $item['quantity']);
			}
		}
		return $subtotal;
	}

	/**
	* Save the datas of the temporary order to the DB
	* @param object $selected_order
	* @param array $items
	* @param string $author
	*/
	public static function saveCurrentOrder($selected_order, $items = null, $author) {
		$orderCollection = static::collection();
		$userCollection = User::collection();
		/************* PREPARING DATAS **************/
		$datas_order_prices = array(
			'total' => $selected_order["total"],
			'subTotal' => $selected_order["subTotal"],
			'handling' => $selected_order["handling"],
			'promocode_disable' => $selected_order["promocode_disable"],
			'comment' => $selected_order["comment"]
		);
		if(!empty($selected_order["tax"])) {
			$datas_order_prices["tax"] = $selected_order["tax"];
		}
		if(!empty($selected_order["credit_used"])) {
			$datas_order_prices["credit_used"] = $selected_order["credit_used"];
		}
		/**************UPDATE DB****************************/
		if(isset($selected_order["user_total_credits"])){
			if(strlen($selected_order["user_id"]) > 10){
				$userCollection->update(array("_id" => new MongoId($selected_order["user_id"])), array('$set' => array("total_credit" => (float) $selected_order["user_total_credits"])));
			} else {
				$userCollection->update(array("_id" => $selected_order["user_id"]), array('$set' => array("total_credit" => (float) $selected_order["user_total_credits"])));
			}
		}
		$orderCollection->update(array("_id" => new MongoId($selected_order["id"])),array('$set' => $datas_order_prices));
		//Update Items
		foreach($items as $item) {
			static::changeQuantity($selected_order["id"], $item["_id"], $item["quantity"], $item["initial_quantity"]);
			static::cancelItem($selected_order["id"], $item["_id"], $item["cancel"]);
		}
		//Pushing modification datas to db
		$modification_datas["author"] = $author;
		$modification_datas["type"] = "items";
		$modification_datas["date"] = new MongoDate(strtotime('now'));
		$modification_datas["comment"] = $selected_order['comment'];
		$result = static::collection()->update(array("_id" => new MongoId($selected_order["id"])),
		array('$push' => array('modifications' => $modification_datas)), array('upsert' => true));
		return $result;
	}

	/**
	* Cancel or Uncancel an item from one Order
	* @param string $order_id
	* @param string $cart_id
	* @param boolean $cancel
	*/
	public static function cancelItem($order_id, $cart_id, $cancel = true) {
		$orderCollection = static::collection();
		$order = $orderCollection->findOne(array("_id" => new MongoId($order_id)), array('items' => 1));
		foreach($order["items"] as $key => $item) {
			if($item["_id"] == new MongoId($cart_id)) {
				$order["items"][$key]["cancel"] = $cancel;
			}
		}
		$orderCollection->update(array("_id" => new MongoId($order_id)), array('$set' => array( "items" => $order["items"])));
	}

	/**
	* Change the quantity of an order
	* @param string $order_id
	* @param string $cart_id
	* @param float $quantity
	* @param float $initial_quantity
	*/
	public static function changeQuantity($order_id, $cart_id, $quantity, $initial_quantity = null) {
		$orderCollection = static::collection();
		$order = $orderCollection->findOne(array("_id" => new MongoId($order_id)), array('items' => 1));
		foreach($order["items"] as $key => $item) {
			if($item["_id"] == new MongoId($cart_id)) {
				$order["items"][$key]["quantity"] = $quantity;
				if(!empty($initial_quantity)) {
					$order["items"][$key]["initial_quantity"] = $initial_quantity;
				}
			}
		}
		$orderCollection->update(array("_id" => new MongoId($order_id)), array('$set' => array( "items" => $order["items"])));
	}

	/**
	* Refresh the prices details and credits of the temporary order
	* and return it to the view
	* @param object $selected_order
	* @param array $items
	*/
	public static function refreshTempOrder($selected_order, $items = null) {
		//Configuration
		$orderCollection = static::collection();
		$userCollection = User::collection();
		//Save items status
		if(!empty($items)){
			$datas_order["items"] = $items;
		}
		//Get Actual Taxes and Handling
		$handling = static::shipping($items);
		$overSizeHandling = static::overSizeShipping($items);
		$tax = static::tax($selected_order,$items);
		$tax = $tax ? $tax + (($overSizeHandling + $handling) * static::TAX_RATE) : 0;
		$subTotal = static::subTotal($items);
		/************PROMOCODES TREATMENT************/
		if(!empty($selected_order["promo_code"])){
			//Get Actual Promocodes variables
			$regexObj = new MongoRegex("/" . $selected_order["promo_code"] . "/i");
			$conditions = array("code" => $regexObj);
			$promocode = Promocode::find("first", $conditions);
			if( $subTotal <= $promocode->minimum_purchase){
				$preAfterDiscount = $subTotal;
				$datas_order["promocode_disable"] = true;
			}
			else {
				$preAfterDiscount = $subTotal  + $selected_order["promo_discount"];
				$datas_order["promocode_disable"] = false;
			}
		} else {
			$preAfterDiscount = $subTotal;
			$datas_order["promocode_disable"] = true;
		}
		/**************CREDITS TREATMENT**************/
		if(isset($selected_order["credit_used"])){
			if(empty($selected_order["user_total_credits"])){
				if(strlen($order["user_id"]) > 10){
					$user_ord = $userCollection->findOne(array("_id" => new MongoId($order["user_id"])));
				} else {
					$user_ord = $userCollection->findOne(array("_id" => $order["user_id"]));
				}
			} else {
				$user_ord["total_credit"] = $selected_order["user_total_credits"];
			}
			$datas_user["total_credit"] = (float) $user_ord["total_credit"];
			//Set Initial Credits if not Set
			if(empty($selected_order["initial_credit_used"])) {
				$datas_order["initial_credit_used"] = $selected_order["credit_used"];
				$selected_order["initial_credit_used"] = $selected_order["credit_used"];
			}
			//CASE (CREDITS > TOTAL)
			if(abs($selected_order["credit_used"]) > $preAfterDiscount) {
				$refill = abs($selected_order["credit_used"] + $preAfterDiscount);
				$new_credits = $selected_order["credit_used"] + $refill;
				$datas_user["total_credit"] = ($datas_user["total_credit"] + $refill);
				$afterDiscount = $preAfterDiscount  + $new_credits;
				$datas_order["credit_used"] = $new_credits;
			} else if(abs($selected_order["credit_used"]) == $preAfterDiscount) {
				$afterDiscount = $preAfterDiscount + $selected_order["credit_used"];
			} else if(abs($selected_order["credit_used"]) < $preAfterDiscount) {
				//Get back credits from user
				$initial_credits = ($user_ord["total_credit"] - abs($selected_order["initial_credit_used"] - $selected_order["credit_used"]));
				if($selected_order["credit_used"] != $selected_order["initial_credit_used"]){
					$datas_user["total_credit"] = $initial_credits;
					}
				if(abs($selected_order["initial_credit_used"]) > $preAfterDiscount) {
					$refill = abs($selected_order["initial_credit_used"] + $preAfterDiscount);
					$new_credits = $selected_order["initial_credit_used"] + $refill;
					$datas_user["total_credit"] = ($refill + $user_ord["total_credit"]);
					$afterDiscount = $preAfterDiscount  + $new_credits;
					$datas_order["credit_used"] = $new_credits;
				} else if(abs($selected_order["initial_credit_used"]) <= $preAfterDiscount) {
					$afterDiscount = $preAfterDiscount + $selected_order["initial_credit_used"];
					$datas_order["credit_used"] = $selected_order["initial_credit_used"];
				}
			}
		}
		/***********END OF CREDITS TREATMENT*************/
		/***********CHECK TAX, HANDLING, TOTAL***********/
		//Check if afterdiscount is negative
		if($afterDiscount < 0){
			$afterDiscount = 0;
		}
		$total = $afterDiscount + $tax + $handling + $overSizeHandling;
		$datas_order_prices = array(
			'total' => $total,
			'subTotal' => $subTotal,
			'tax' => $tax,
			'handling' => $handling,
			'promocode_disable' => $datas_order["promocode_disable"],
			'credit_used' => $selected_order["credit_used"]
		);
		$datas_order = array_merge($datas_order_prices, $datas_order);
		$new_datas_order = $orderCollection->findOne(array("_id" => new MongoId($selected_order["id"])));
		$new_datas_order = array_merge($new_datas_order, $datas_order);
		//keep user credits infos
		$new_datas_order["user_total_credits"] = $datas_user["total_credit"];
		$new_datas_order['initial_credit_used'] = $selected_order["initial_credit_used"];
		/**************CREATE TEMP ORDER********************/
		$temp_order = static::Create($new_datas_order);
		return $temp_order;
	}
}

?>