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
	 * The # of business days to be added to an event to determine the estimated
	 * ship by date. The default is 18 business days.
	 *
	 * @var int
	 **/
	protected $_shipBuffer = 18;

	/**
	 * Any holidays that need to be factored into the estimated ship date calculation.
	 *
	 * @var array
	 */
	protected $_holidays = array();

	public function index() {
		$user = Session::read('userLogin');
		$orders = Order::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user['_id']),
			'order' => array('date_created' => 'DESC')
		));
		foreach ($orders as $order) {
			$shipDate["$order->_id"] = $this->shipDate($order);
		}
		return (compact('orders', 'shipDate'));
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
		$user = Session::read('userLogin');
		$order = Order::find('first', array(
			'conditions' => array(
				'order_id' => $order_id,
				'user_id' => (string) $user['_id']
		)));
		$new = ($order->date_created->sec > (time() - 120)) ? true : false;
		$shipDate = $this->shipDate($order);
		if (!empty($shipDate)) {
			$allEventsClosed = ($this->getLastEvent($order)->end_date->sec > time()) ? false : true;
		} else {
			$allEventsClosed = true;
		}
		$shipped = (isset($order->tracking_numbers)) ? true : false;
		$preShipment = ($shipped) ? true : false;
		$itemsByEvent = $this->itemGroupByEvent($order);
		$orderEvents = $this->orderEvents($order);

		return compact(
			'order',
			'orderEvents',
			'itemsByEvent',
			'new',
			'shipDate',
			'allEventsClosed',
			'shipped',
			'preShipment'
		);
	}

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

		return $vars + compact('cartEmpty', 'cartByEvent', 'error', 'orderEvents');
	}

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
		$tax = 0;
		$shippingCost = 0;
		$overShippingCost = 0;
		$billingAddr = $shippingAddr = null;
		//var_dump($cart->data());
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

		if ($shippingAddr) {
			$tax = array_sum($cart->tax($shippingAddr));
			$shippingCost = Cart::shipping($cart, $shippingAddr);
			$overShippingCost = Cart::overSizeShipping($cart);
		}

		$map = function($item) { return $item->sale_retail * $item->quantity; };
		$subTotal = array_sum($cart->map($map)->data());

		$userDoc = User::find('first', array('conditions' => array('_id' => $user['_id'])));

		$orderCredit = Credit::create();

		if (Session::read('credit')) {
			$orderCredit->credit_amount = Session::read('credit');
		}

		if (isset($this->request->data['credit_amount'])) {
			$credit = number_format((float)$this->request->data['credit_amount'], 2);
			$lower = -0.999;
			$upper = (!empty($userDoc->total_credit)) ? $userDoc->total_credit + 0.01 : 0;
			$inRange = Validator::isInRange($credit, null, compact('lower', 'upper'));
			$isMoney = Validator::isMoney($credit);
			if (!$isMoney) {
				$orderCredit->error = "Please apply credits that are in the form of $0.00";
				$orderCredit->errors(
					$orderCredit->errors() + array('amount' => "Please apply credits that are in the form of $0.00")
				);
			}
			if (!$inRange) {
				$orderCredit->errors(
					$orderCredit->errors() + array(
						'amount' => "Please apply credits that are greater than $0 and less than $$userDoc->total_credit"
					));
			}
			$isValid = ($subTotal >= $credit) ? true : false;
			if (!$isValid) {
				$orderCredit->errors(
					$orderCredit->errors() + array(
						'amount' => "Please apply credits that is $$subTotal or less"
					));
			}
			if ($isMoney && $inRange && $isValid) {
				$orderCredit->credit_amount = -$credit;
				Session::write('credit', -$credit, array('name' => 'default'));
			}
		}

		$orderPromo = Promotion::create();
		$postCreditTotal = $subTotal + $orderCredit->credit_amount;
		if (Session::read('promocode')) {
			$orderPromo->code = Session::read('promocode');
		}
		if (isset($this->request->data['code'])) {
			$orderPromo->code = $this->request->data['code'];
		}

		if ($orderPromo->code) {
			$code = Promocode::confirmCode($orderPromo->code);
			$count = Promotion::confirmCount($code->_id, $user['_id']);
			if ($code) {
				if ($code->max_use > 0) {
					if ($count >= $code->max_use) {
						$orderPromo->errors(
							$orderPromo->errors() + array(
								'promo' => "Sorry, you've reached the maximum allowed use of this promotion code"
						));
					}
				}
				if ($code->limited_use == true) {
					$userPromotions = ($userDoc->promotions) ? $userDoc->promotions->data() : null;
					if (!is_array($userPromotions) || !in_array((string) $code->_id, $userPromotions)) {
						$orderPromo->errors(
							$orderPromo->errors() + array(
								'promo' => "Sorry, this promotion is limited"
						));
					}
				}
				if ($postCreditTotal > $code->minimum_purchase) {
					$orderPromo->user_id = $user['_id'];
					if ($code->type == 'percentage') {
						$orderPromo->saved_amount = $postCreditTotal * -$code->discount_amount;
					}
					if ($code->type == 'dollar') {
						$orderPromo->saved_amount = -$code->discount_amount;
					}
					Session::write('promocode', $orderPromo->code, array('name' => 'default'));
				} else {
					$orderPromo->errors(
						$orderPromo->errors() + array(
							'promo' => "Sorry, you need a minimum order total of $$code->minimum_purchase to use promotion code. Shipping and sales tax is not included."
					));
				}
			} else {
				$orderPromo->errors(
					$orderPromo->errors() + array(
						'promo' => 'Sorry, Your promotion code is invalid'
				));
			}
			$errors = $orderPromo->errors();
			if ($errors) {
				$orderPromo->saved_amount = 0;
			}
		}

		$vars = compact(
			'user', 'billing', 'shipping', 'cart', 'subTotal', 'order',
			'tax', 'shippingCost', 'overShippingCost' ,'billingAddr', 'shippingAddr', 'orderCredit', 'orderPromo', 'userDoc', 'discountExempt'
		);

		if (($cart->data()) && (count($this->request->data) > 1) && $order->process($user, $data, $cart, $orderCredit, $orderPromo)) {
			$order->order_id = strtoupper(substr((string)$order->_id, 0, 8).substr((string)$order->_id, 13, 4));
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
			$order->ship_date = new MongoDate($this->shipDate($order));
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
				'shipDate' => $this->shipDate($order)
			);
			Silverpop::send('orderConfirmation', $data);
			return $this->redirect(array('Orders::view', 'args' => $order->order_id));
		}

		$cartEmpty = ($cart->data()) ? false : true;

		return $vars + compact('cartEmpty', 'order', 'cartByEvent', 'orderEvents');

	}

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
	 * Calculated estimated ship by date for an order.
	 *
	 * The estimated ship-by-date is calculated based on the last event that closes.
	 * @param object $order
	 * @return string
	 */
	public function shipDate($order) {
		$i = 1;
		$event = $this->getLastEvent($order);
		$shipDate = null;
		if (!empty($event)) {
			$shipDate = $event->end_date->sec;
			while($i < $this->_shipBuffer) {
				$day = date('N', $shipDate);
				$date = date('Y-m-d', $shipDate);
				if ($day < 6 && !in_array($date, $this->_holidays)) {
					$i++;
				}
				$shipDate = strtotime($date.' +1 day');
			}
		}
		return $shipDate;
	}

	/**
	 * Return the event that will be the last to close in an order.
	 *
	 * This method is needed to determine what the expected ship date should be.
	 * Based on the business model, if a multi event order will ship together then the
	 * estimated ship date will be determined from the fulfillment of the last event.
	 * @param object $order
	 * @return object $event
	 */
	public function getLastEvent($order) {
		$event = null;
		$ids = $this->getEventIds($order);
		if (!empty($ids)) {
			$event = Event::find('first', array(
				'conditions' => array('_id' => $ids),
				'order' => array('date_created' => 'DESC')
			));
		}
		return $event;
	}

	/**
	 * Group all the items in an order by their corresponding event.
	 *
	 * The $order object is assumed to have originated from one of model types; Order or Cart.
	 * Irrespective of the type both will return an associative array of event items.
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
	 * Return all the events of an order.
	 */
	public function orderEvents($object) {
		$orderEvents = null;
		$ids = $this->getEventIds($object);
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

	/**
	 * Get all the eventIds that are stored either in an order or cart object and cast to MongoId.
	 * @param object
	 * @return array
	 */
	protected function getEventIds($object) {
		$items = (!empty($object->items)) ? $object->items->data() : $object->data();
		$event = null;
		$ids = array();
		foreach ($items as $item) {
			$eventId = (!empty($item['event_id'])) ? $item['event_id'] : $item['event'][0];
			if (!empty($eventId)) {
				$ids[] = new MongoId("$eventId");
			}
		}
		return $ids;
	}
}

?>