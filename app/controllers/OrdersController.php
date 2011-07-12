<?php

namespace app\controllers;

use app\models\User;
use app\models\Cart;
use app\models\Item;
use app\models\Credit;
use app\models\Address;
use app\models\Order;
use app\models\Event;
use app\models\Promotion;
use app\models\Promocode;
use app\models\Affiliate;
use app\models\OrderShipped;
use app\models\Service;
use app\controllers\BaseController;
use lithium\storage\Session;
use lithium\util\Validator;
use MongoDate;
use MongoId;
use li3_silverpop\extensions\Silverpop;

/**
 * The Orders Controller
 *
 * @see http://admin.totsy.local/docs/admin/controllers/OrdersController
 **/
class OrdersController extends BaseController {

	/**
	 * Allows the view of all the orders a customer has in descending order.
	 * The ship date is also populated next to each order if applicable.
	 * @return compact
	 */
	public function index() {
		$itemsCollection = Item::collection();
		$lifeTimeSavings = 0;
		$user = Session::read('userLogin');
		$orders = Order::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user['_id']),
			'order' => array('date_created' => 'DESC')
		));
		$trackingNumbers = array();
		foreach ($orders as $key => $order) {
			$list = $trackingNum = array();
			$shipDate["$order->_id"] = Cart::shipDate($order);
			$conditions = array('OrderId' => $order->_id);
			$shipRecords = OrderShipped::find('all', compact('conditions'));
			foreach ($shipRecords as $record) {
				if (!in_array($record->{'Tracking #'}, $list)) {
					$list[] = $record->{'Tracking #'};
					$shipMethod = (empty($record->ShipMethod) ? 'UPS' : $record->ShipMethod);
					$trackingNum[] = array('code' => $record->{'Tracking #'}, 'method' => $shipMethod);
				}
			}
			if ($trackingNum) {
				$trackingNumbers["$order->_id"] = $trackingNum;
			}
			//Calculatings LifeTime Savings
			if (empty($order["cancel"])) {
				$savings = 0;
				foreach ($order["items"] as $item) {
					$itemInfo = $itemsCollection->findOne(array("_id" => new MongoId($item["item_id"])));
					if (empty($item->cancel)) {
						$lifeTimeSavings += $item["quantity"] * ($itemInfo['msrp'] - $itemInfo['sale_retail']);
						$savings += $item["quantity"] * ($itemInfo['msrp'] - $itemInfo['sale_retail']);
					}
				}
				$orderSavings[$key] = $savings;
			}
		}
		return (compact('orders', 'shipDate', 'trackingNumbers', 'orderSavings', 'lifeTimeSavings'));
	}

	/**
	 * View a specific order.
	 *
	 * This method gets the order for a user based on their order number and
	 * user_id. There is a time check on the order to determine if a new.
	 * The view is called both for the order confirmation page and the order view page.
	 * @param string $order_id
	 * @return mixed
	 */
	public function view($order_id) {
		$itemsCollection = Item::collection();
		$user = Session::read('userLogin');
		$order = Order::find('first', array(
			'conditions' => array(
				'order_id' => $order_id,
				'user_id' => (string) $user['_id']
		)));
		$new = ($order->date_created->sec > (time() - 120)) ? true : false;
		$shipDate = Cart::shipDate($order);
		if (!empty($shipDate)) {
			$allEventsClosed = (Cart::getLastEvent($order)->end_date->sec > time()) ? false : true;
		} else {
			$allEventsClosed = true;
		}
		$shipped = (isset($order->tracking_numbers)) ? true : false;
		$shipRecord = (isset($order->ship_records)) ? true : false;
		$preShipment = ($shipped || $shipRecord) ? true : false;
		$itemsByEvent = $this->itemGroupByEvent($order);
		$orderEvents = $this->orderEvents($order);
		//Check if all items from one event are closed
		foreach($itemsByEvent as $items_e) {
			foreach($items_e as $item) {
				if(empty($item['cancel'])) {
					$openEvent[$item['event_id']] = true;
				}
			}
		}
		$pixel = Affiliate::getPixels('order', 'spinback');
		$spinback_fb = Affiliate::generatePixel('spinback', $pixel, array('order' => $_SERVER['REQUEST_URI']));
		//Get Items Skus - Analytics
		foreach($itemsByEvent as $key => $event) {
			foreach($event as $key_b => $item) {
				$itemRecord = Item::find($item['item_id']);
				if (!empty($itemRecord)) {
					$itemsByEvent[$key][$key_b]['sku'] = $itemRecord->sku_details[$item['size']];
				}
			}
		}
		//Calculatings Savings
		$savings = 0;
		foreach ($order->items as $item) {
			$itemInfo = $itemsCollection->findOne(array("_id" => new MongoId($item["item_id"])));
			if (empty($item->cancel)) {
				$savings += $item["quantity"] * ($itemInfo['msrp'] - $itemInfo['sale_retail']);
			}
		}
		return compact(
			'order',
			'orderEvents',
			'itemsByEvent',
			'new',
			'shipDate',
			'allEventsClosed',
			'shipped',
			'preShipment',
			'spinback_fb',
			'shipRecord',
			'preShipment',
			'openEvent',
			'savings'
		);
	}

	/**
	 * Creates inital order based on the cart.
	 * This view is needed to set the address information for the order.
	 * When the add method is processed then the applicable tax can be calculated.
	 * @return compact
	 * @todo improve documentation
	 */
	public function add() {
		$data = $this->request->data;
		Session::delete('credit');
		Session::delete('promocode');
		$user = Session::read('userLogin');
		$billing = Address::menu($user);
		$shipping = Address::menu($user);
		$fields = array(
			'item_id',
			'color',
			'category',
			'description',
			'product_weight',
			'quantity',
			'sale_retail',
			'size',
			'url',
			'primary_image',
			'expires',
			'event_name',
			'event'
		);

		$order = Order::create();

		if (Cart::increaseExpires()){
			$cart = Cart::active(array(
				'fields' => $fields,
				'time' => '-5min'
			));
			$cartByEvent = $this->itemGroupByEvent($cart);
			$orderEvents = $this->orderEvents($cart);
			$cart = Cart::active(array('fields' => $fields, 'time' => '-3min'));
		}

		$map = function($item) { return $item->sale_retail * $item->quantity; };
		$subTotal = array_sum($cart->map($map)->data());
		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal','order',
			'tax', 'shippingCost', 'billingAddr', 'shippingAddr'
		);

		$cartEmpty = ($cart->data()) ? false : true;

		if ($this->request->data) {
			if (count($this->request->data) > 1) {
				$user = Session::read('userLogin');
				$user['checkout'] = $this->request->data;
				Session::write('userLogin', $user, array('name' => 'default'));
				$this->redirect('Orders::process');
			} else {
				$error = "Shipping and Delivery Information Missing";
			}
		}
		$savings = Session::read('userSavings');
		$shipDate = Cart::shipDate($cart);
		//calculate savings
		$savings = Session::read('userSavings');
		return $vars + compact('cartEmpty', 'cartByEvent', 'error', 'orderEvents', 'shipDate', 'savings');
	}

	public function addShipping() {
		$usersCollection = User::Collection();
		$fields = array(
			'item_id',
			'color',
			'category',
			'description',
			'product_weight',
			'quantity',
			'sale_retail',
			'size',
			'url',
			'primary_image',
			'expires',
			'event_name',
			'event'
		);
		if ($user = Session::read('userLogin')) {
			$address = Address::first(array(
				'conditions' => array('user_id' => (string) $user['_id'])
			));
		}
		$cart = Cart::active(array(
				'fields' => $fields,
				'time' => '-5min'
		));
		$cartEmpty = ($cart->data()) ? false : true;
		return compact('address','cartEmpty');
	}
	
	/**
	 * Processes an order by capturing payment.
	 * @return compact
	 * @todo Improve documentation
	 * @todo Make this method lighter by taking out promocode/credit validation
	 */
	public function process() {
		$order = Order::create();
		$user = Session::read('userLogin');
		$data = $user['checkout'] + $this->request->data;
		$fields = array(
			'item_id',
			'color',
			'category',
			'description',
			'product_weight',
			'quantity',
			'sale_retail',
			'size',
			'url',
			'primary_image',
			'expires',
			'event',
			'discount_exempt'
		);
		$cart = Cart::active(array('fields' => $fields, 'time' => 'now'));
		$shipDate = Cart::shipDate($cart);
		$cartByEvent = $this->itemGroupByEvent($cart);
		
		$orderEvents = $this->orderEvents($cart);
		$discountExempt = $this->_discountExempt($cart);
		foreach ($cart as $cartValue) {
			$event = Event::find('first', array(
				'conditions' => array('_id' => $cartValue->event[0])
			));
			$cartValue->event_name = $event->name;
			$cartValue->event_id = $cartValue->event[0];
			unset($cartValue->event);
		}
		$shippingCost = 0;
		$overShippingCost = 0;
		$billingAddr = $shippingAddr = null;
		if (isset($data['billing_shipping']) && $data['billing_shipping'] == '1') {
			$data['shipping'] = $data['billing'];
		}

		foreach (array('billing', 'shipping') as $key) {
			$var = $key . 'Addr';

			if (isset($data[$key])) {
				$addr = $data[$key];
				${$var} = Address::find('first', array(
					'conditions' => array(
						'_id' => $addr,
						'user_id' => (string) $user['_id']
				)));
			}
		}
		$tax = 0;
		if ($shippingAddr) {
			$tax = array_sum($cart->tax($shippingAddr));
			$shippingCost = Cart::shipping($cart, $shippingAddr);
			$overShippingCost = Cart::overSizeShipping($cart);
			$tax = $tax ? $tax + (($overShippingCost + $shippingCost) * Cart::TAX_RATE) : 0;
			$tax =  $tax + $shippingCost + $overShippingCost;
		}
		/**
		*	Handling services the user may be eligible for
		*	@see app\models\Service::freeShippingCheck()
		**/
		$service = Session::read('services', array('name' => 'default'));
		extract(Service::freeShippingCheck($shippingCost, $overShippingCost));

		$map = function($item) { return $item->sale_retail * $item->quantity; };
		$subTotal = array_sum($cart->map($map)->data());

		$userDoc = User::find('first', array('conditions' => array('_id' => $user['_id'])));

		$orderCredit = Credit::create();
		$credit_amount = isset($this->request->data['credit_amount']) ? $this->request->data['credit_amount'] : 0;
		$orderCredit->checkCredit($credit_amount, $subTotal, $userDoc);

		$orderPromo = Promotion::create();
		$orderServiceCredit = Service::tenOffFiftyCheck($subTotal);
		$postServiceCredit = $subTotal + $orderServiceCredit;
		$postCreditTotal = $postServiceCredit + $orderCredit->credit_amount;

		$promo_code = NULL;
		if (Session::read('promocode')) {
			$promo_code = Session::read('promocode');
		}
		if (array_key_exists('code', $this->request->data)) {
			$promo_code = $this->request->data['code'];
		}

		$orderPromo->promoCheck($promo_code, $userDoc, compact('postCreditTotal', 'shippingCost', 'overShippingCost'));
		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal', 'order',
			'tax', 'shippingCost', 'overShippingCost' ,'billingAddr', 'shippingAddr', 'orderCredit', 'orderPromo', 'orderServiceCredit','freeshipping','userDoc', 'discountExempt'
		);
		if (($cart->data()) && (count($this->request->data) > 1) && $order->process($user, $data, $cart, $orderCredit, $orderPromo)) {
			$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
			if ($orderCredit->credit_amount) {
				User::applyCredit($user['_id'], $orderCredit->credit_amount);
				Credit::add($orderCredit, $user['_id'], $orderCredit->credit_amount, "Used Credit");
				Session::delete('credit');
				$order->credit_used = $orderCredit->credit_amount;
			}
			if ($orderPromo->saved_amount) {
				Promocode::add((string) $code->_id, $orderPromo->saved_amount, $order->total);
				$orderPromo->order_id = (string) $order->_id;
				$orderPromo->code_id = (string) $code->_id;
				$orderPromo->date_created = new MongoDate();
				$orderPromo->save();
				$order->promo_code = $orderPromo->code;
				$order->promo_discount = $orderPromo->saved_amount;
			}
			if ($service) {
				$services = array();
				if (array_key_exists('freeshipping', $service) && $service['freeshipping'] === 'eligible') {
					$services = array_merge($services, array("freeshipping"));
				}
				if (array_key_exists('10off50', $service) && $service['10off50'] === 'eligible') {
					$order->discount = -10.00;
					$services = array_merge($services, array("10off50"));
				}
				$order->service = $services;
			}
			if (!empty($orderPromo->type)) {
				if ($orderPromo->type == 'free_shipping') {
					Promocode::add((string) $code->_id, 0, $order->total);
					$orderPromo->order_id = (string) $order->_id;
					$orderPromo->code_id = (string) $code->_id;
					$orderPromo->date_created = new MongoDate();
					$orderPromo->save();
					$order->promo_code = $orderPromo->code;
				}
			}
			$order->ship_date = new MongoDate(Cart::shipDate($order));
			$order->save();
			Cart::remove(array('session' => Session::key('default')));
			foreach ($cart as $item) {
				Item::sold($item->item_id, $item->size, $item->quantity);
			}
			$user = User::getUser();
			++$user->purchase_count;
			$user->save(null, array('validate' => false));
			$data = array(
				'order' => $order,
				'email' => $user->email,
				'shipDate' => $shipDate
			);
			Silverpop::send('orderConfirmation', $data);
			//Re Initialize userSavings
			Session::write('userSavings', 0);
			if (array_key_exists('freeshipping', $service) && $service['freeshipping'] === 'eligible') {
				Silverpop::send('nextPurchase', $data);
			}
			return $this->redirect(array('Orders::view', 'args' => $order->order_id));
		}
		$cartEmpty = ($cart->data()) ? false : true;
		//calculate final savings
		$savings = Session::read('userSavings');
		if(!empty($orderPromo->saved_amount) && !empty($savings)) {
			$savings += abs($orderPromo->saved_amount);
		}
		if(!empty($orderCredit->credit_amount) && !empty($savings)) {
			$savings += abs($orderCredit->credit_amount);
		}

		if(!empty($_GET['json'])) {
			echo json_encode($vars + compact('cartEmpty', 'order', 'cartByEvent', 'orderEvents', 'shipDate', 'savings'));
		} else {
			return $vars + compact('cartEmpty', 'order', 'cartByEvent', 'orderEvents', 'shipDate', 'savings');
		}
	}

	/**
	 * Checks if the discountExempt flag is set in any of the cart items.
	 * The method will return true if there is a discounted item and false if there isn't.
	 *
	 * @param array
	 * @return boolean
	 */
	protected function _discountExempt($cart) {
		$discountExempt = false;
		foreach ($cart as $cartItem) {
			if ($cartItem->discount_exempt) {
				$discountExempt = true;
			}
		}
		return $discountExempt;
	}

	/**
	 * Group all the items in an order by their corresponding event.
	 *
	 * The $order object is assumed to have originated from one of model types; Order or Cart.
	 * Irrespective of the type both will return an associative array of event items.
	 *
	 * @param object $order
	 * @return array $eventItems
	 */
	protected function itemGroupByEvent($object) {
		$eventItems = null;
		if ($object) {
			$model = $object->model();
			if ($model == 'app\models\Order') {
				$orderItems = $object->items->data();
				foreach ($orderItems as $item) {
					$eventItems[$item['event_id']][] = $item;
				}
			}
			if ($model == 'app\models\Cart') {
				$orderItems = $object->data();
				foreach ($orderItems as $item) {
					$event = $item['event'][0];
					unset($item['event']);
					$eventItems[$event][] = $item;
				}
			}
		}
		return $eventItems;
	}

	/**
	*
	**/
	/**public function applyPromo() {
		$this->render(array('layout' => false));
		var_dump('ajax');
		$userDoc = User::find('first', array('conditions' => array('_id' => $user['_id'])));
		$data = $this->request->data;
		if (isset($this->request->data['code'])) {
			$promo_code = $this->request->data['code'];
		}
		$orderPromo->promoCheck($promo_code, $userDoc, compact('postCreditTotal', 'shippingCost', 'overShippingCost'));
		return json_encode($orderPromo->error());
	}**/


	/**
	 * Return all the events of an order.
	 *
	 * @param object $object
	 * @return array $orderEvents
	 */
	public function orderEvents($object) {
		$orderEvents = null;
		$ids = Cart::getEventIds($object);
		if (!empty($ids)) {
			$events = Event::find('all', array(
				'conditions' => array('_id' => $ids),
				'fields' => array('name', 'ship_message', 'ship_date', 'url')
			));
			$events = $events->data();
			foreach ($events as $event) {
				$orderEvents[$event['_id']] = $event;
			}
		}

		return $orderEvents;
	}
	
	public function payment() {
	
	
	}

}

?>