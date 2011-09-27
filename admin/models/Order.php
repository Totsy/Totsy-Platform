<?php

namespace admin\models;

use MongoId;
use MongoDate;
use MongoRegex;
use li3_payments\extensions\payments\exceptions\TransactionException;
use lithium\analysis\Logger;
use admin\models\User;
use admin\models\Item;
use admin\models\Credit;

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
class Order extends Base {

	const TAX_RATE = 0.08875;

	const TAX_RATE_NYS = 0.04375;

	protected static $_classes = array(
		'tax' => 'admin\extensions\AvaTax',
		'payments' => 'li3_payments\extensions\Payments'
	);

	protected $_meta = array('source' => 'orders');

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

	public $validates = array(
		'authKey' => 'Could not secure payment.',
	);

	public static function dates($name) {
		return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	/**
	 * Case insensitive lookup of an order by its ID.
	 *
	 * @param string $orderId
	 * @return object
	 */
	public static function lookup($orderId) {
		$orderId = new MongoRegex("/$orderId/i");
		$result = static::find('first', array('conditions' => array(
			'order_id' => $orderId
		)));
		return $result;
	}

	/**
	 * Voids an Order
	 *
	 * @param array $order - Array of order information
	 * @return boolean
	 */
	public static function void($order) {
		$payments = static::$_classes['payments'];

		$collection = static::collection();
		$orderId = new MongoId($order['_id']);
		try {
		    $error = null;
		    if ($order['total'] != 0 && is_numeric($order['authKey'])){
                $auth = $payments::void('default', $order['authKey']);
			} else {
			    $auth = -1;
			    $error = "Can't capture because total is zero.";
			}
			return $collection->update(
                    array('_id' => $orderId),
                    array('$set' => array(
                        'void_date' => new MongoDate(),
                        'void_confirm' => $auth),
                        'auth_error' => $error),
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

	/**
	 * Processes an order.
	 *
	 * @fixme This could be refactored as a concrete record method. It
	 *        currently is static for backwards compat. with documents
	 *        retrieved via native methods.
	 * @see OrdersController::update()
	 * @param array The order to process. Required fields are 'authKey', 'total' and '_id'.
	 * @return boolean
	 */
	public static function process($order) {
		$payments = static::$_classes['payments'];

		$collection = static::collection();
		$orderId = new MongoId($order['_id']);

		try {
		    $error = null;
		    if ($order['total'] != 0 && is_numeric($order['authKey'])) {
                $auth = $payments::capture('default', $order['authKey'], floor($order['total']*100)/100);
            } else {
                $auth = -1;
                $error = "Can't capture because total is zero.";
            }
                Logger::info("process-payment: Processed payment for order_id $order[_id]");
                return $collection->update(
                    array('_id' => $orderId),
                    array('$set' => array(
                        'payment_date' => new MongoDate(),
                        'auth_confirmation' => $auth,
                        'auth_error' => $error)),
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
	 * @param boolean $credits_recored - prevent the user from being
	 * twice if admin user cancelled all line items instead of just
	 * clicking cancel order
	 */
	public static function cancel($order_id, $author, $comment, $credits_recorded = false, $test = false) {
		$tax = static::$_classes['tax'];

		$userCollection = User::collection();
		//Get the actual datas of the order
		$result = static::find('first', array('conditions' => array(
			'_id' => $order_id instanceof MongoId ? $order_id : new MongoId($order_id)
		)));
		$order = $result->data();
		if(strlen($order["user_id"]) > 10){
			$user = $userCollection->findOne(array("_id" => new MongoId($order["user_id"])));
		} else {
			$user = $userCollection->findOne(array("_id" => $order["user_id"]));
		}
		$item_amount = 0;
		//Compare the cancel status, write modification datas and update cancel db status
		$modification_datas["author"] = $author;
		$modification_datas["date"] = new MongoDate(strtotime('now'));
		if(empty($order["cancel"]) || ($order["cancel"] == false)){
			$modification_datas["type"] = "cancel";
			$modification_datas["comment"] = $comment;
			static::collection()->update(array('_id' => new MongoId($order_id)),
				array('$set' => array('cancel' => true)), array("upsert" => true));
			//Authorize.Net Void
			if (!$test) {
			    static::void($order);
			}
			//Push cancel status to Avalara
			$tax::cancelTax($order['order_id']);

			//Cancel all the items
			$items = $order["items"];
			$item_names = array();
			foreach($order["items"] as $key => $item){
				$items[$key]["cancel"] = true;
				//Reattribute original quantity
				$item_amount += $item['sale_retail'];
				$item_names[] = $item['description'];
				if(!empty($items[$key]["initial_quantity"])) {
					$items[$key]["quantity"] = $items[$key]["initial_quantity"];
				}
			}
			static::collection()->update(array('_id' => new MongoId($order_id)),
				array('$set' => array('items' => $items)));
		}
		//Reattribute credits to the user
		if(!$credits_recorded && isset($order["credit_used"])) {
			if(strlen($order["user_id"]) > 10){
			    $new_credit = $order['credit_used'];
				$creditData = array(
						'user_id' => $order['user_id'],
						'order_id' => $order['_id'],
						'order_number' => $order['order_id'],
						 'sign' => '+',
						 'amount' => (string) abs($new_credit),
						'reason' => "Credit Adjustment",
						'description' => "Credit Returned to user. Line Item(s) were cancelled from order  $order[order_id]."
					);
				Credit::add($creditData);
				$userCollection->update(array(
				    "_id" => new MongoId($order["user_id"])),
				    array('$set' => array(
				        "total_credit" => (((float) abs($new_credit)) +
				        ((float) $user["total_credit"])))
				    ));
			} else {
				$userCollection->update(array("_id" => $order["user_id"]), array('$set' => array("total_credit" => (((float) abs($new_credit)) + ((float) $user["total_credit"])))));
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
				$nycExempt = ($nysZip && $item['sale_retail'] < 110) ? true : false;
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
	* Save the data of the temporary order to the DB.
	*
	* @param object $selected_order
	* @param array $items
	* @param string $author
	*/
	public static function saveCurrentOrder($selected_order, $items = null, $author) {
		$orderCollection = static::collection();
		$userCollection = User::collection();
		$credits_recorded = false;
		/************* PREPARING DATAS **************/
		$selected_order += array(
			'order_id' => null,
			'total' => null,
			'subTotal' => null,
			'handling' => null,
			'promo_discount' => null,
			'promocode_disable' => null,
			'comment' => null,
			'initial_credit_used' => null
		);
		$datas_order_prices = array(
			'total' => (float) $selected_order["total"],
			'subTotal' => (float) $selected_order["subTotal"],
			'handling' => (float) $selected_order["handling"],
			'overSizeHandling' => (float) $selected_order["overSizeHandling"],
			'handlingDiscount' => (float) $selected_order["handlingDiscount"],
			'overSizeHandlingDiscount' => (float) $selected_order["overSizeHandlingDiscount"],
			'promo_discount' => (float) $selected_order["promo_discount"],
			'discount' => (float) $selected_order["discount"],
			'promocode_disable' => $selected_order["promocode_disable"],
			'comment' => $selected_order["comment"]
		);
		if (array_key_exists('original_credit_used', $selected_order)) {
		   $datas_order_prices['original_credit_used'] = $selected_order["original_credit_used"];
		}
		if(!empty($selected_order["tax"])) {
			$datas_order_prices["tax"] = $selected_order["tax"];
		}
		if(!empty($selected_order["credit_used"])) {
			$datas_order_prices["credit_used"] = (float) $selected_order["credit_used"];
		}
		/**************UPDATE TAX****************************/
		extract(static::recalculateTax($selected_order,$items,true));
		/**************UPDATE DB****************************/
		if (array_key_exists('original_credit_used', $selected_order)) {
		    $new_credit = $selected_order['original_credit_used'] - (float) number_format($selected_order['credit_used'],2);
		    $new_credit = abs((float)$selected_order['original_credit_used']) - (float) $selected_order['credit_used'];
		}
		if(isset($selected_order["user_total_credits"])){
			if (array_key_exists('original_credit_used', $selected_order)) {
                $new_credit = $selected_order['original_credit_used'] - (float) $selected_order['credit_used'];
                $new_credit = abs($new_credit);
            } else {
                $new_credit = (float) $selected_order['original_credit_used'] - (float) $selected_order['credit_used'];
                 $new_credit = abs($new_credit);
            }
            $creditReturnData = array(
                    'user_id' => $selected_order['user_id'],
                    'order_id' => $selected_order['id'],
                    'order_number' => $selected_order['order_id'],
                    'sign' => '+',
                    'amount' => (string) $new_credit,
                    'reason' => "Credit Adjustment",
                    'description' => "Credit Returned to user. Line Item(s) were cancelled from order $selected_order[order_id]."
                );
            Credit::add($creditReturnData,array('type' => 'Credit Refund'));

			if(strlen($selected_order["user_id"]) > 10){
				$userCollection->update(array("_id" => new MongoId($selected_order["user_id"])), array('$set' => array("total_credit" => (float) $selected_order["user_total_credits"])));
			} else {
				$userCollection->update(array("_id" => $selected_order["user_id"]), array('$set' => array("total_credit" => (float) $selected_order["user_total_credits"])));
			}
			$credits_recorded = true;
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
		return compact('result', 'credits_recorded');
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
	* and return it to the view.
	*
	* @param object $selected_order
	* @param array $items
	*/
	public static function refreshTempOrder($selected_order, $items = null) {
		//Configuration
		$orderCollection = static::collection();
		$userCollection = User::collection();
		$promocodeCollection = Promocode::collection();
		//Save items status
		if(!empty($items)){
			$datas_order["items"] = $items;
		}
		//Get Actual Taxes
		extract(static::recalculateTax($selected_order,$items));

		if (is_object($tax)) {
            //Avatax::totsyCalculateTax($selected_order);
            //$tax = static::tax($selected_order,$items);
            //$tax = $tax ? $tax + (($overSizeHandling + $handling) * static::TAX_RATE) : 0;
		}
		$subTotal = static::subTotal($items);
		/************PROMOCODES TREATMENT************/
		if(!empty($selected_order["promo_code"])){
			//Get Actual Promocodes variables
			$regexObj = new MongoRegex("/" . $selected_order["promo_code"] . "/i");
			$conditions = array("code" => $regexObj);
			$promocode = $promocodeCollection->findOne($conditions);
			if($subTotal <= $promocode['minimum_purchase']) {
				$preAfterDiscount = $subTotal;
				$datas_order["promocode_disable"] = true;
				#Reset Shipping
				if ($promocode['type'] == 'free_shipping') {
					$datas_order["handlingDiscount"] = 0;
					$datas_order["overSizeHandlingDiscount"] = 0;
					$preAfterDiscount = $subTotal;
				}
			} else {
				if ($promocode['type'] == 'percentage') {
					$selected_order["promo_discount"] = - ($subTotal * $promocode['discount_amount']);
					$datas_order["promo_discount"] = $selected_order["promo_discount"];
				}
				$preAfterDiscount = $subTotal + $selected_order["promo_discount"];
				if ($promocode['type'] == 'free_shipping') {
					$datas_order["handlingDiscount"] = $selected_order["handling"];
					$datas_order["overSizeHandlingDiscount"] = $selected_order["overSizeHandling"];
					$preAfterDiscount = $subTotal - $datas_order["handlingDiscount"] - $datas_order["overSizeHandlingDiscount"];
				}
				$datas_order["promocode_disable"] = false;
			}
		} else {
			$preAfterDiscount = $subTotal;
			$datas_order["promocode_disable"] = true;
		}
		/************SERVICES TREATMENT**************/
		if(!empty($selected_order["service"])) {
			if ($selected_order["service"] == '10off50') {
				if($subTotal >= 50.00) {
					$datas_order["discount"] = 10.00;
					$preAfterDiscount -= 10;
				} else {
					$datas_order["discount"] = 0.00;
				}
			} else {
				$preAfterDiscount -= $selected_order["discount"];
			}
		}
		/**************CREDITS TREATMENT**************/
		if($selected_order["credit_used"] != ('' || null)) {
			$selected_order["credit_used"] = (float) - abs($selected_order["credit_used"]);
			if(empty($selected_order["user_total_credits"])){
				if(strlen($selected_order["user_id"]) > 10){
					$user_ord = $userCollection->findOne(array("_id" => new MongoId($selected_order["user_id"])));
				} else {
					$user_ord = $userCollection->findOne(array("_id" => $selected_order["user_id"]));
				}
			} else {
				$user_ord["total_credit"] = $selected_order["user_total_credits"];
			}
			if(!empty($user_ord["total_credit"])) {
				$datas_user["total_credit"] = (float) $user_ord["total_credit"];
			} else {
				$datas_user["total_credit"] = 0;
				$user_ord["total_credit"] = 0;
			}
			//Set Initial Credits if not Set
			if(empty($selected_order["original_credit_used"])) {
				$datas_order["original_credit_used"] = $selected_order["credit_used"];
				$selected_order["original_credit_used"] = $selected_order["credit_used"];
			}
			//CASE (CREDITS > TOTAL)
			if(abs($selected_order["credit_used"]) > $preAfterDiscount) {
				$refill = abs($selected_order["credit_used"] + $preAfterDiscount);
				$new_credits = $selected_order["credit_used"] + $refill;
				$datas_user["total_credit"] = ($datas_user["total_credit"] + $refill);
				$afterDiscount = $preAfterDiscount + $new_credits;
				$datas_order["credit_used"] = $new_credits;
			} else if(abs($selected_order["credit_used"]) == $preAfterDiscount) {
				$afterDiscount = $preAfterDiscount + $selected_order["credit_used"];
			} else if(abs($selected_order["credit_used"]) < $preAfterDiscount) {
				//Get back credits from user
				$initial_credits = ($user_ord["total_credit"] - abs($selected_order["original_credit_used"] - $selected_order["credit_used"]));
				if($selected_order["credit_used"] != $selected_order["original_credit_used"]) {
					$datas_user["total_credit"] = $initial_credits;
				}
				if(abs($selected_order["original_credit_used"]) > $preAfterDiscount) {
					$refill = abs($selected_order["original_credit_used"] + $preAfterDiscount);
					$new_credits = $selected_order["original_credit_used"] + $refill;
					$datas_user["total_credit"] = ($refill + $user_ord["total_credit"]);
					$afterDiscount = $preAfterDiscount  + $new_credits;
					$datas_order["credit_used"] = $new_credits;
				} else if(abs($selected_order["original_credit_used"]) <= $preAfterDiscount) {
					$afterDiscount = $preAfterDiscount + $selected_order["original_credit_used"];
					$datas_order["credit_used"] = $selected_order["original_credit_used"];
				}
			}
			if($afterDiscount < 0) {
				$afterDiscount = 0;
			}
		} else {
			$afterDiscount = $preAfterDiscount;
		}
		/***********END OF CREDITS TREATMENT*************/
		/***********CHECK TAX, HANDLING, TOTAL***********/
		$total = $afterDiscount + $tax + $selected_order["handling"] + $selected_order["overSizeHandling"];
		$datas_order_prices = array(
			'total' => $total,
			'subTotal' => $subTotal,
			'tax' => $tax,
			'promocode_disable' => $datas_order["promocode_disable"],
			'credit_used' => (float) $selected_order["credit_used"]
		);
		if (array_key_exists('original_credit_used', $selected_order)) {
		   $datas_order_prices['original_credit_used'] = $selected_order["original_credit_used"];
		}
		$datas_order = array_merge($datas_order_prices, $datas_order);
		$new_datas_order = $orderCollection->findOne(array("_id" => new MongoId($selected_order["id"])));
		$new_datas_order = array_merge($new_datas_order, $datas_order);
		//keep user credits infos
		$new_datas_order["user_total_credits"] = $datas_user["total_credit"];
		$new_datas_order['original_credit_used'] = $selected_order["original_credit_used"];
		/**************CREATE TEMP ORDER********************/
		$temp_order = static::Create($new_datas_order);
		return $temp_order;
	}

	/**
	 * Method to recalculate sales tax for renewated order. Tax is based on a Avalara.
	 * SK: I hope they calculate sales tax based on sipping destination ;)
	 *
	 * @param object $current_order
	 * @param array $itms
	 */
	protected static function recalculateTax ($current_order,$itms,$update=false){
		$tax = static::$_classes['tax'];

		$orderCollection = static::collection();
		$order = $orderCollection->findOne(
			array("_id" => new MongoId($current_order["id"])),
			array(
				'billing'=>1,
				'shipping'=>1,
				'order_id'=>1
			)
		);
		$items = array();
		foreach($itms as $itm){
			if(($itm["cancel"] == false) || (empty($itm["cancel"]))){
				$items[] = $itm;
			}
		}
		if ($update === false){
			$ordermodel = __CLASS__;
			return $tax::getTax(compact('order','items','ordermodel','current_order','itms'));
		} else {
			$admin = 1;
			$tax::cancelTax($order['order_id']);
			return (array) $tax::commitTax(compact('order','items','admin'));
		}
	}

	/**
	* This function checks if a given order has been canceled or any of its items has been canceled
	* @params (string) $order_id : short id of the order
	* @return false if the order has never been canceled or has canceled items, else it returns true
	**/
	public static function checkForCancellations($order_id){
	    $cancel_count = Order::collection()->count(array(
	        'order_id' => $order_id,
	        '$or' => array(
	            array('items.cancel' => true),
	            array('cancel' => true)
	        )
	    ));
	    if ($cancel_count == 0) {
	        return false;
	    } else {
	        return true;
	    }
	}

	/**
	* This function returns the any orders that have been errored
	**/
	public static function orderPaymentRequests($requests) {
	    $orderColl = static::collection();
	    $conditions = array();
		$payments = array();
		$message = "";
		$type = null;

        if($requests) {
            if (array_key_exists('capture', $requests) && !empty($requests['capture'])) {

				    $capture = static::collection()->find(array('order_id' => array(
				        '$in' => $requests['capture'])),
				        array(
				        'authKey' => 1,
				        'total' => 1,
				        'order_id' => 1,
				        '_id' => 1
				    ));
				    foreach($capture as $order) {
				        static::process($order);
				    }
				    $requests['type'] = 'error';
				    $requests['start_date'] = date('m/d/Y');
				    $message = " Capture Process has completed.  Here are today's failed captures.";
			}
			if (array_key_exists('todays',$requests) && !empty($requests['todays'])) {
				$conditions = array('error_date' => array('$gte' => new MongoDate(strtotime(date("m/d/Y") . "00:00:00"))));
			} else {
				if (array_key_exists('search',$requests) && !empty($requests['search'])) {
					$conditions = array('order_id' => $requests['search']);
				} else {
					switch($requests['type']){
						case 'processed':
							$type = 'processed';
							if (array_key_exists('end_date',$requests) && !empty($requests['end_date'])) {
								$conditions['payment_date'] = array_merge($conditions['payment_date'],array('$lte' => new MongoDate(strtotime($requests['end_date']))));
							}else {
								$conditions['payment_date'] = array_merge($conditions['payment_date'],array('$lte' => new MongoDate()));
							}

							if (array_key_exists('start_date',$requests) && !empty($requests['start_date'])) {
								$conditions['payment_date'] = array('$gte' => new MongoDate(strtotime($requests['start_date'])));
							} else {
							    $conditions = array();
							}
							break;
						case 'expired':
							$type = 'expired';
							$expirtion_date = mktime(0,0,0,date('m'),date('d') + 3, date('Y') );
							$order_date_created_min = mktime(0,0,0,date('m',$expirtion_date),date('d',$expirtion_date) - 30, date('Y',$expirtion_date) );
							$order_date_created_max = mktime(23,59,59,date('m',$expirtion_date),date('d',$expirtion_date) - 30, date('Y',$expirtion_date) );
							$conditions['date_created'] = array('$gte' => new MongoDate($order_date_created_min), '$lte' => new MongoDate($order_date_created_max));
							$conditions['auth_confirmation'] = array('$exists' => false);
							$conditions['ship_records'] = array('$exists' => false);
							break;
						case 'error':
							$type = 'error';
							if (array_key_exists('end_date',$requests) && !empty($requests['end_date'])) {
								$conditions['error_date'] = array('$lt' => new MongoDate(strtotime($requests['end_date'] . " 23:59:59")));
							}else {
								$conditions['error_date'] = array('$lt' => new MongoDate());
							}
							if (array_key_exists('start_date',$requests) && !empty($requests['start_date'])) {
								$conditions['error_date'] = array_merge($conditions['error_date'],array('$gte' => new MongoDate(strtotime($requests['start_date'] . " 00:00:00"))));
							} else {
							    $conditions = array();
							}
							break;
						default:
							break;
					}
				}
			}
			if (!empty($conditions)) {
                $payments = $orderColl->find($conditions, array(
                    '_id' => 1,
                    'auth_error' => 1,
                    'order_id' => 1,
                    'error_date' => 1,
                    'date_created' => 1,
                    'payment_date' => 1,
                    'authKey' => 1,
                    'auth_confirmation' => 1,
                    'total' => 1
                ));
			}
		}
		return compact('payments','type', 'message');
	}

	/**
	* Returns true, if order passed in payment capture failed, otherwise return false
	* @params (string) $orderId : short id of the order
	* @return boolean
	**/

	public static function failedCaptureCheck($orderId = null) {
	    $failed = false;
	    $coll = static::collection();
	    $count = $coll->count(array('order_id' => $orderId, 'payment_date' => array('$exists' => true)));
	     if ($count == 0) {
	        $failed = true;
	     }

	     return $failed;
	}
}

?>