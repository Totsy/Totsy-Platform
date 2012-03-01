<?php

namespace admin\models;

use MongoId;
use MongoDate;
use MongoRegex;
use lithium\analysis\Logger;
use lithium\core\Environment;
use lithium\storage\Session;
use admin\extensions\Mailer;
use admin\models\User;
use admin\models\Item;
use admin\models\Credit;
use li3_payments\extensions\adapter\payment\CyberSource;
use li3_payments\payments\Processor;
use Exception;

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
		'payments' => 'li3_payments\payments\Processor'
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

	/**
	 * The # of business days to be added to an event to determine the estimated
	 * ship by date. The default is 18 business days.
	 *
	 * @var int
	 **/
	protected $_shipBuffer = 15;
	
	/**
	 * Any holidays that need to be factored into the estimated ship date calculation.
	 *
	 * @var array
	 */
	protected $_holidays = array('2010-11-25', '2010-11-26');
	
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
	 * @see li3_payments\payments\Processor::void()
	 * @param array $order - Array of order information
	 * @return boolean
	 */
	public static function void($order) {
		$payments = static::$_classes['payments'];

		$orderId = new MongoId($order['_id']);
		$error = null;
		$data = array(
			'void_date' => new MongoDate()
		);

		if ($order['total'] == 0 || !is_numeric($order['authKey'])) {
			$data['void_confirm'] = -1;
			$error = "Can't void because total is zero.";
		} else if ($order['card_type'] == 'amex') {
			$data['void_confirm'] = -1;
			$error = "Can't void because the card type is Amex.";
		} else if ($order['authTotal'] == 0) {
			$data['void_confirm'] = -1;
			$error = "Can't void because authorization amount is 0";
		} else {
			if(!empty($order['auth'])) {
				$transaction = $order['auth'];
			} else {
				$transaction = $order['authKey'];
			}
			$auth = $payments::void('default', $transaction, array(
				'processor' => isset($order['processor']) ? $order['processor'] : null,
				'orderID' => $order['order_id']
			));

			if ($auth->success()) {
				$data['void_confirm'] = $auth->key;
			} elseif ($auth->errors) {
				$data['void_confirm'] = -1;

				$message  = "Void failed for order id `{$order['_id']}`:";
				$message .= $error = implode('; ', $auth->errors);
				Logger::error($message);
			} else {
				$data['void_confirm'] = -1;

				$message  = "Void failed for order id `{$order['_id']}`.";
				$error = 'Unknown error.';
				Logger::error($message);
			}
		}
		$update = static::update(
			array(
				'$set' => $data + array(
					'auth_error' => $error
				)
			),
			array('_id' => $orderId),
			array('upsert' => false)
		);
		return $update && !$error;
	}

	public static function findUnshippedItems($order) {
		$unshipped_items = array();
		$ordersShippedCollection = OrderShipped::collection();
		$order_items = array();
		
		// Remove digital items and canceled items
		foreach ($order['items'] as $item) {
			if(empty($item['digital']) && empty($item['cancel']))
				$order_items[]=$item;
		}

		// Get the SKUs for all items in the order to match against the ship records
		$itemSkus = Item::getSkus($order_items);
		
		// Retrieve all of the orders.shipped documents
		$ship_records = $ordersShippedCollection->find(
			array('_id' => 
				array('$in' => 
					$order['ship_records']
				)
			)
		);
		
		// Remove all shipped items from the itemSkus array
		foreach ($ship_records as $ship_record) {
			if (isset($itemSkus[$ship_record['SKU']]))
				unset($itemSkus[$ship_record['SKU']]);
		}
		
		if (!empty($itemSkus)) {
			// items still in itemsSkus were not shipped
			foreach ($itemSkus as $sku => $item) {
				// If the sku is empty we can't trust the data and skip this item
				if (!empty($sku))
					$unshipped_items[] = $item['_id'];
			}
		}
		
		return $unshipped_items;
	}

	/**
	 * Processes an order.
	 *
	 * @fixme This could be refactored as a concrete record method. It
	 *        currently is static for backwards compat. with documents
	 *        retrieved via native methods.
	 * @see li3_payments\payments\Processor::capture()
	 * @see OrdersController::update()
	 * @param array The order to process. Required fields are 'authKey', 'total' and '_id'.
	 * @return boolean
	 */
	public static function process($order) {
		$payments = static::$_classes['payments'];

		$order = is_object($order) ? $order->data() : $order;
		$orderId = new MongoId($order['_id']);
		$error = null;
		$data = array();

		Logger::info("Processing payment for order id `{$order['_id']}`.");
		#If Digital Items, Calculate correct Amount
		$amountToCapture = static::getAmountNotCaptured($order);
		if ($order['total'] == 0) {
			$data['auth_confirmation'] = $order['authKey'];
			$data['payment_date'] = new MongoDate();
			Logger::error("Can't capture because total is zero.");
		} else {
			$auth = $payments::capture(
				'default',
				$order['authKey'],
				floor($amountToCapture * 100) / 100,
				array(
					'processor' => isset($order['processor']) ? $order['processor'] : null,
					'orderID' => $order['order_id']
				)
			);
			if ($auth->success()) {
				Logger::info("Order Succesfully Captured");
				$data['auth_confirmation'] = $auth->key;
				$data['payment_date'] = new MongoDate();
				#Save Capture in Transactions Logs
				$transation['authKey'] = $auth->key;
				$transation['amount'] = $amountToCapture;
				$transation['date_captured'] = new MongoDate();
				#Update the Money Captured field
				if($order['captured_amount']) {
					$totalAmountCaptured = ($amountToCapture + $order['captured_amount']);
				} else {
					$totalAmountCaptured = $amountToCapture;
				}
				$update = static::update(
					array('$push' => array('capture_records' => $transation),
						  '$set' => array('captured_amount' => $totalAmountCaptured)
					),
					array('_id' => $orderId)
				);
			} elseif ($auth->errors) {
				$data['auth_confirmation'] = -1;
				$data['error_date'] = new MongoDate();

				$message  = "Processing of payment for order id `{$order['_id']}` failed:";
				$message .= $error = implode('; ', $auth->errors);
				Logger::info($message);
			} else {
				$data['auth_confirmation'] = -1;
				$data['error_date'] = new MongoDate();
				$error = 'Unknown error.';

				$message = "Processing of payment for order id `{$order['_id']}` failed.";
				Logger::info($message);
			}
		}
		$update = static::update(
			array(
				'$set' => $data + array(
					'auth_error' => $error
				)
			),
			array('_id' => $orderId),
			array('upsert' => false)
		);
		return $update && !$error;
	}

	public static function setTrackingNumber($id, $number) {
		return static::update(
			array('$addToSet' => array('tracking_numbers' => $number)),
			array('order_id' => $id)
		);
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
				'billing.lastname'
			),
			'address' => array(
				'shipping.address',
				'billing.address'
			)
		);
		foreach ($keys[$type] as $key) {
			$conditions[] = array($key => new MongoRegex("/$data/i"));
		}
		$orders = static::collection();
		$date = array('date_created' => array('$gt' => new MongoDate(strtotime('August 3, 2010'))));
		return $orders->find(array('$or' => $conditions) + $date)->sort(array('date_created' => 1));
	}
	
	public static function uncancel($order_id, $author) {
		//Get the actual datas of the order
		$result = static::find('first', array('conditions' => array(
			'_id' => $order_id instanceof MongoId ? $order_id : new MongoId($order_id)
		)));
		$order = $result->data();
		$modification_datas["author"] = $author;
		$modification_datas["date"] = new MongoDate(strtotime('now'));
		$modification_datas["type"] = "uncancel";
		$items = $order["items"];
		$item_names = array();
		foreach($order["items"] as $key => $item) {
			$items[$key]["cancel"] = false;
			//Reattribute original quantity
			$item_amount += $item['sale_retail'];
			$item_names[] = $item['description'];
			if(!empty($items[$key]["initial_quantity"])) {
				$items[$key]["quantity"] = $items[$key]["initial_quantity"];
			}
		}
		static::collection()->update(
			array('_id' => new MongoId($order_id)),
			array('$set' => array('items' => $items, 'cancel'=>false),
				'$push' => array('modifications' => $modification_datas)
			)
		);
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
		// Pushing modification datas to db.
		return static::collection()->update(
			array("_id" => new MongoId($order_id)),
			array('$push' => array('modifications' => $modification_datas)),
			array('upsert' => true)
		);
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
		$payments = static::$_classes['payments'];
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
			'promo_discount' => (float) $selected_order["promo_discount"],
			'promocode_disable' => $selected_order["promocode_disable"]
		);
		echo 'test0';
		if(static::isOnlyDigital(array('items' => $items))) {
			$datas_order_prices['isOnlyDigital'] = true;
			#Reverse Soft Auth or Full Auth that Failed
			$orderToVoidAuth = static::find('first', array('conditions' => array('order_id' => $selected_order['order_id'])));
			echo 'test';
			if(!empty($orderToVoidAuth['authKey']) && empty($orderToVoidAuth['auth_confirmation'])) {
				echo 'test2';
				var_dump($orderToVoidAuth['processor']);
				var_dump($orderToVoidAuth['order_id']);
				var_dump($orderToVoidAuth['authKey']);
				#Save Old AuthKey with Date
				$newRecord = array('authKey' => $orderToVoidAuth['authKey'], 'date_saved' => new MongoDate());
				$void = $payments::void('default', $orderToVoidAuth['authKey'], array(
					'processor' => isset($orderToVoidAuth['processor']) ? $orderToVoidAuth['processor'] : null,
					'orderID' => $orderToVoidAuth['order_id']
				));
				$error = implode('; ', $void->errors);
				var_dump($error);
				var_dump($void->key);
				if($void->success()) {
					echo 'test3';
					#Add to Auth Records Array
					$update = $ordersCollection->update(
						array('_id' => $orderToVoidAuth['_id']),
						array('$push' => array('auth_records' => $newRecord),
							  '$unset' => array('authKey' => 1, 'auth' => 1, 'authTotal' => 1)
						)
					);
				}
			}
		} else {
			$datas_order_prices['isOnlyDigital'] = false;
		}
		#Refreshing Shipdate depending of the order type (Digital/Physical)
		if($items) {
			$shipDate = static::shipDate(array('items' => $items));
			if(!empty($shipDate)) {
				$datas_order_prices['ship_date'] = $shipDate;
			}
		}
		if(isset($selected_order["overSizeHandling"])) {
			$datas_order_prices['overSizeHandling'] = (float) $selected_order["overSizeHandling"];
		}
		if(isset($selected_order["discount"])) {
			$datas_order_prices['discount'] = (float) $selected_order["discount"];
		}
		if(isset($selected_order["handlingDiscount"])) {
			$datas_order_prices['handlingDiscount'] = (float) $selected_order["handlingDiscount"];
		}
		if(isset($selected_order["overSizeHandlingDiscount"])) {
			$datas_order_prices['overSizeHandlingDiscount'] = (float) $selected_order["overSizeHandlingDiscount"];
		}
		if(!empty($selected_order["comment"])) {
			$datas_order_prices['comment'] = $selected_order["comment"];
		}
		if (array_key_exists('original_credit_used', $selected_order)) {
		   $datas_order_prices['original_credit_used'] = $selected_order["original_credit_used"];
		}
		if(isset($selected_order["tax"])) {
			$datas_order_prices["tax"] = $selected_order["tax"];
		}
		if(isset($selected_order["credit_used"])) {
			$datas_order_prices["credit_used"] = (float) $selected_order["credit_used"];
		}
		if(isset($selected_order['isOnlyDigital'])) {
			$datas_order_prices["isOnlyDigital"] = $selected_order["isOnlyDigital"];
		}
		if(!empty($selected_order['payment_date'])) {
			$datas_order_prices["payment_date"] = new MongoDate();
		}
		if(!empty($selected_order['auth_confirmation'])) {
			$datas_order_prices["auth_confirmation"] = $selected_order["auth_confirmation"];
		}
		die();
		/**************UPDATE TAX****************************/
		// Is this even used?
		extract(static::_recalculateTax($selected_order,$items,true));
		/**************UPDATE DB****************************/
		if (array_key_exists('original_credit_used', $selected_order)) {
		    $new_credit = $selected_order['original_credit_used'] - (float) number_format($selected_order['credit_used'],2);
		    $new_credit = abs((float)$selected_order['original_credit_used']) - (float) $selected_order['credit_used'];
		}
		if(isset($selected_order["user_total_credits"]) && $selected_order["user_total_credits"] != 0){
			if (array_key_exists('original_credit_used', $selected_order)) {
                $new_credit = $selected_order['original_credit_used'] - (float) abs($selected_order['credit_used']);
                $new_credit = abs($new_credit);
            } else {
                $new_credit = (float) $selected_order['original_credit_used'] - (float) abs($selected_order['credit_used']);
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
			$shortshipped = null;
			if(!empty($item["shortshipped"])) {
				$shortshipped = true;
			}
			static::changeQuantity($selected_order["id"], $item["_id"], $item["quantity"], $item["initial_quantity"]);
			static::cancelItem($selected_order["id"], $item["_id"], $item["cancel"], $shortshipped);
		}
		//Pushing modification datas to db
		$modification_datas["author"] = $author;
		$modification_datas["type"] = "items";
		$modification_datas["date"] = new MongoDate(strtotime('now'));
		if (array_key_exists('comment', $selected_order)) {
			$modification_datas["comment"] = $selected_order['comment'];
		}
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
	public static function cancelItem($order_id, $cart_id, $cancel = true, $shortshipped = null) {
		$orderCollection = static::collection();
		$order = $orderCollection->findOne(array("_id" => new MongoId($order_id)), array('items' => 1));

		foreach($order["items"] as $key => $item) {
			if($item["_id"] == new MongoId($cart_id)) {
				$order["items"][$key]["cancel"] = $cancel;
				if($shortshipped) {
					$order["items"][$key]["shortshipped"] = true;
				}
			}
		}
		return $orderCollection->update(
			array("_id" => new MongoId($order_id)),
			array('$set' => array("items" => $order["items"]))
		);
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
		//Get Actual Taxes and Handling
		$total = 0;
		$afterDiscount = 0;

		if(static::isOnlyDigital($datas_order)) {
			$selected_order['handling'] = 0;
			$selected_order['overSizeHandling'] = 0;
			$selected_order['tax'] = 0;
			$datas_order['overSizeHandling'] = 0;	
			$datas_order['handling'] = 0;
			$datas_order['tax'] = 0;
			$datas_order['isOnlyDigital'] = true;
			$datas_order['payment_date'] = true;
			if($selected_order['capture_records']) {
				$datas_order['auth_confirmation'] = $selected_order['capture_records'][0]['authKey'];
			} else {
   				$datas_order['auth_confirmation'] = 1;
			}
		} else {
			$datas_order['handling'] = static::shipping($items);
			$datas_order['overSizeHandling'] = static::overSizeShipping($items);
			$selected_order['handling'] = $datas_order['handling'];
			$selected_order['overSizeHandling'] = $datas_order['overSizeHandling'];
			extract(static::_recalculateTax($selected_order,$items));
			$datas_order['tax'] = $tax;
		}
		if ($tax instanceof Exception) {
			/* Rethrow exceptions received while recalculating tax. */
			throw $tax;
		}
		if (is_object($tax)) {
            //Avatax::totsyCalculateTax($selected_order);
            //$tax = static::tax($selected_order,$items);
            //$tax = $tax ? $tax + (($overSizeHandling + $handling) * static::TAX_RATE) : 0;
		}
		$afterDiscount = $subTotal = static::subTotal($items);
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
					$selected_order["promo_discount"] =+ ($subTotal * $promocode['discount_amount']);
					$datas_order["promo_discount"] = $selected_order["promo_discount"];
				}
				$preAfterDiscount = $subTotal - $selected_order["promo_discount"];
				if($preAfterDiscount < 0) {
					$preAfterDiscount = 0;
				}
				if ($promocode['type'] == 'free_shipping' && !static::isOnlyDigital($datas_order)) {
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
				if(!static::isOnlyDigital($datas_order)) {
					$datas_order["discount"] = (abs($datas_order['handling']) + abs($datas_order['overSizeHandling']));
					$preAfterDiscount -= $datas_order["discount"];
				} else {
					$datas_order["discount"] = 0.00;
					$datas_order["handlingDiscount"] = 0.00;
					$datas_order["overSizeHandlingDiscount"] = 0.00;
				}
			}
		}
		/**************CREDITS TREATMENT**************/
		if($selected_order["credit_used"] != ('' || null)) {
			$selected_order["credit_used"] = (float) - abs($selected_order["credit_used"]);
			if($selected_order["original_credit_used"]) {
				$selected_order["original_credit_used"] = (float) - abs($selected_order["original_credit_used"]);
			}
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
		$total = $afterDiscount + $tax;
		if(!empty($selected_order["handling"])) {
			$total += $selected_order["handling"];
		}
		if(!empty($selected_order["overSizeHandling"])) {
			$total += $selected_order["overSizeHandling"];
		}
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
	protected static function _recalculateTax($current_order, $itms, $update=false) {
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
				    $capture = static::collection()->find(
						array(
							'order_id' => array('$in' => $requests['capture'])
						),
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
							$conditions['auth_confirmation'] = array('$ne' => -1);
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
							$conditions['cancel'] = array('$exists' => false);
							$conditions['auth_confirmation'] = -1;
							$conditions['ship_records'] = array('$exists' => true);
							$conditions['payment_captured'] = array('$exists' => false);
							break;
						case 'failed_reauth':
							$type = 'failed_reauth';
							$conditions['auth_confirmation'] = array('$exists' => false);
							$conditions['payment_date'] = array('$exists' => false);
							$conditions['cancel'] = array('$exists' => false);
							$conditions['payment_captured'] = array('$exists' => false);
							$conditions['auth_error'] = array('$exists' => true);
							$conditions['error_date'] = array('$exists' => true);
							$conditions['ship_records'] = array('$exists' => false);
							$conditions['$where'] = 'this.total == this.authTotal';
							break;
						case 'failed_initial_auth':
							$type = 'failed_initial_auth';
							$conditions['auth_confirmation'] = array('$exists' => false);
							$conditions['payment_date'] = array('$exists' => false);
							$conditions['cancel'] = array('$exists' => false);
							$conditions['payment_captured'] = array('$exists' => false);
							$conditions['auth_error'] = array('$exists' => true);
							$conditions['error_date'] = array('$exists' => true);
							$conditions['ship_records'] = array('$exists' => false);
							$conditions['$where'] = 'this.total != this.authTotal';
							$conditions['$or'] = array(array('authTotal' => 1), array('authTotal' => 0));
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
	
		public static function getCCinfos($order = null) {
		$creditCard = null;
		if(!empty($order['cc_payment'])) {
			$cc_encrypt = $order['cc_payment'];
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
			$iv =  base64_decode($order['cc_payment']['vi']);
			$key = $order['user_id'];
			unset($cc_encrypt['vi']);
			foreach	($cc_encrypt as $k => $cc_info) {
				$crypt_info = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key . $k), base64_decode($cc_info), MCRYPT_MODE_CFB, $iv);
				$creditCard[$k] = $crypt_info;
			}
		}
		return $creditCard; 
	}
	
	public static function getStatus($order) {
		$status = 'Idle';
		if($order['authTotal'] != $order['total']) {
			$status = 'Soft Authorized';
		}
		if($order['ship_records'] || $order['isOnlyDigital']) {
			$status = 'Shipped';
		}
		if($order['payment_date']) {
			$status = 'Captured but not Shipped';
		}
		if($order['payment_date'] && ($order['ship_records'] || $order['isOnlyDigital'])) {
			$status = 'Shipped And Captured';
		}
		if($order['cancel']) {
			$status = 'Canceled';
		}
		return $status;
	}
	
	/**
	 * Encrypt all credits card informations with MCRYPT and store it in the Session
	 */
	public static function creditCardEncrypt($cc_infos, $user_id) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$cc_encrypt['vi'] = base64_encode($iv);
		foreach	($cc_infos as $k => $cc_info) {
			$crypt_info = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($user_id . $k), $cc_info, MCRYPT_MODE_CFB, $iv);
			$cc_encrypt[$k] = base64_encode($crypt_info);
		}
		return $cc_encrypt;
	}
	
	/**
	 * Decrypt all credits card processed with Auth.Net
	 */
	public static function getCCinfosByTheOldWay($order) {
		$creditCard = null;
		if(!empty($order['cc_payment'])) {
			$cc_encrypt = $order['cc_payment'];
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
			$iv =  base64_decode($order['cc_payment']['vi']);
			$key = md5($order['user_id']);
			unset($cc_encrypt['vi']);
			foreach  ($cc_encrypt as $k => $cc_info) {
				$crypt_info = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key.sha1($k), base64_decode($cc_info), MCRYPT_MODE_CFB, $iv);
				$creditCard[$k] = $crypt_info;
			}
		}
		return $creditCard;
	}
	
	/**
	 * This function is used to send out various emails with order update or cancelation details.
	 * The 'Order_Update' and 'Cancel_Order' templates are currently used
	 * @param object $order
	 * @param string $email_template
	 * @see admin\controllers\OrdersController->manage_items()
	 * @see admin\controllers\OrdersController->view()
	 */
	public static function sendEmail($order, $email_template) {
		$userCollection = User::collection();
		$current_user = Session::read('userLogin');
		if(strlen($order["user_id"]) > 10){
			$user = $userCollection->findOne(array("_id" => new MongoId($order->user_id)));
		} else {
			$user = $userCollection->findOne(array("_id" => $order->user_id));
		}
		if (is_object($order->ship_date))
			$shipDate = $order->ship_date->sec;
		else if (is_string($order->ship_date))
			$shipDate = strtotime(substr($order->ship_date,0,10));
		else 
			$shipDate = $order->ship_date;
		$data = array(
			'order' => $order->data(),
			'shipDate' => date('M d, Y', $shipDate)
		);
		if (Environment::get() == 'production')
			Mailer::send($email_template, $user["email"], $data);
		else
			Mailer::send($email_template, $current_user["email"], $data);
	}

	/* Check if Items in Order are only Digital
	 * @return boolean onlyDigital
	 */
	public static function isOnlyDigital($order) {
		$onlyDigital = true;
		foreach($order['items'] as $item) {
			if(empty($item['digital']) && empty($item['cancel'])) {
				$onlyDigital = false;
			}
		}
		return $onlyDigital;
	}
	
	public static function getAmountNotCaptured($order) {
		$amountNotCaptured = 0;
		if(!empty($order['captured_amount'])) {
			$amountNotCaptured = ($order['total'] - $order['captured_amount']);
		} else {
			$amountNotCaptured = $order['total'];
		}
		return $amountNotCaptured;
	}
	
	/**
	 * Calculated estimated ship by date for an order.
	 * The estimated ship-by-date is calculated based on the last event that closes.
	 * @param object $order
	 * @return string
	 */
	public static function shipDate($order) {
		$i = 1;
		if(static::isOnlyDigital($order)) {
			$delayDelivery = 5;	    
		} else {
			$delayDelivery = static::_object()->_shipBuffer;
		}
		$shipDate = null;
		$items = (is_object($order)) ? $order->items->data() : $order['items'];
		if (!empty($items)) {
			foreach ($items as $item) {
				if (!empty($item['event_id'])) {
					$ids[] = new MongoId($item['event_id']);
				}
			}
			if (!empty($ids)) {
				$event = Event::find('first', array(
					'conditions' => array('_id' => $ids),
					'order' => array('end_date' => 'DESC')
				));
				$shipDate = is_object($event->end_date) ? $event->end_date->sec : $event->end_date;
				while($i < $delayDelivery) {
					$day = date('D', $shipDate);
					$date = date('Y-m-d', $shipDate);
					if ((($day != 'Sat') && ($day != 'Sun')) && !in_array($date, static::_object()->_holidays)){
						$i++;
					}
					$shipDate = strtotime($date . ' +1 day');
				}
			}
		}
		return $shipDate;
	}
}

?>
