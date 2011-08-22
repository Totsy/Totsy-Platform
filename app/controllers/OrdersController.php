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
use app\models\CreditCard;
use app\models\Promocode;
use app\models\Affiliate;
use app\models\OrderShipped;
use app\models\Service;
use app\controllers\BaseController;
use lithium\storage\Session;
use lithium\util\Validator;
use MongoDate;
use MongoId;
use app\extensions\AvaTax;
use app\extensions\Mailer;

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
		var_dump($order_id);
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

	/**
	 * The user choose his shipping address for his order by :
	 * - Adding a new one
	 * - Selecting one already saved
	 * @return compact
	 */
	public function shipping() {
		$user = Session::read('userLogin');
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
		#Prepare datas
		$address = null;
		$selected = null;
		$cartExpirationDate = 0;
		#Check Datas Form
		if (!empty($this->request->data)) {
			$datas = $this->request->data;
			# If address selected ddwn, get infos from DB
			if (!empty($datas['address_id'])) {
				$address = Address::first(array(
					'conditions' => array('_id' => new MongoId($datas['address_id'])
				)));
			} else {
				$address = Address::create($datas);
				#Check Infos and save it on Session
				if ($address->validates()) {
					Session::write('shipping', $datas);
					#If no address is link with the user, save the current one
					$count = Address::count(array('user_id' => $user['_id']));
					if ($count == 0) {
						$address->user_id = $user['_id'];
						$address->save();
					}
					$this->redirect(array('Orders::payment'));
				} else {
					$error = true;
				}
			}
		} 
		#Get all addresses of the current user
		$addresses = Address::all(array(
			'conditions' => array('user_id' => (string) $user['_id'])
		));		
		#Prepare addresses datas for the dropdown
		if (!empty($addresses)) {
			$idx = 0;
			foreach ($addresses as $value) {
				if ((($idx == 0 || $value['default'] == '1') && empty($datas['address_id'])))
					$address = $value;
				#Get selected ddwn address
				if((string)$value['_id'] == $address['_id']) 
					$selected = (string) $value['_id'];
				$addresses_ddwn[(string)$value['_id']] = $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['address']; 
				$idx++;
			}
		}
		#Check Cart Validty
		$cart = Cart::active(array(
				'fields' => $fields,
				'time' => '-0min'
		));
		foreach($cart as $item){
			if($cartExpirationDate < $item['expires']->sec) {
				$cartExpirationDate = $item['expires']->sec;
			}
		}
		$cartEmpty = ($cart->data()) ? false : true;
		return compact('address','addresses_ddwn','cartEmpty','error','selected','cartExpirationDate');
	}
	
	/**
	 * Processes an order by capturing payment.
	 * @return compact
	 * @todo Improve documentation
	 */
	public function review() {
		#Get Users Informations
		$user = Session::read('userLogin');
		$userDoc = User::find('first', array('conditions' => array('_id' => $user['_id'])));
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
		#Get Current Cart
		$cart = $taxCart = Cart::active(array('fields' => $fields, 'time' => 'now'));
		$cartEmpty = ($cart->data()) ? false : true;
		$cartByEvent = $this->itemGroupByEvent($cart);
		$orderEvents = $this->orderEvents($cart);
		#Calculate Shipped Date
		$shipDate = Cart::shipDate($cart);
		#Check If Discount could be Applied
		$discountExempt = $this->_discountExempt($cart);
		#Get Value Of Each and Sum It
		$subTotal = 0;
		foreach ($cart as $cartValue) {
			$event = Event::find('first', array(
				'conditions' => array('_id' => $cartValue->event[0])
			));
			$cartValue->event_name = $event->name;
			$cartValue->event_id = $cartValue->event[0];
			$subTotal += $cartValue->quantity * $cartValue->sale_retail;
			unset($cartValue->event);
		}
		#Get Shipping / Billing Infos + Costs
		$shippingCost = 0;
		$overShippingCost = 0;
		$shippingAddr = Session::read('shipping');
		$billingAddr = Session::read('billing');
		if ($shippingAddr) {
			$shippingCost = Cart::shipping($cart, $shippingAddr);
			$overShippingCost = Cart::overSizeShipping($cart);
		}
		#Get current Discount
		$vars = Cart::getDiscount($shippingCost, $overShippingCost,$this->request->data);
		#Calculate savings
		$userSavings = Session::read('userSavings');
		$savings = $userSavings['items'] + $userSavings['discount'] + $userSavings['services'];
		#Calculate subTotal with the 10$off
		$postDiscount = ($subTotal - $vars['services']['tenOffFitfy']);
		#Calculate Total with Credits, Promo, Service and Tax
		if (!empty($vars['cartCredit'])) {
			$credits = Session::read('credit');
		 	$vars['postDiscountTotal'] -= $credits;
		}
		#Get Promocodes and eventual Shipping Discount
		if (!empty($vars['cartPromo']['saved_amount'])) {
		 	$promocode = Session::read('promocode');
		 	if($promocode['type'] === 'free_shipping') {
				$shipping_discount = $shippingCost + $overShippingCost;
			}
		}
		#Check Free Shipping Services
		if (!empty($services['freeshipping']['enable'])) {
			$shipping_discount = $shippingCost + $overShippingCost;
		}
		#Getting Tax by Avatax
		$avatax = AvaTax::getTax(compact(
			'cartByEvent', 'billingAddr', 'shippingAddr', 'shippingCost', 'overShippingCost',
			'orderCredit', 'orderPromo', 'orderServiceCredit', 'taxCart'));
		$tax = $avaTax['tax'];
		#Calculate Order Total
		$total = $vars['postDiscountTotal'] + $tax;
		#Read Credit Card Informations
		$creditCard = Order::creditCardDecrypt((string)$user['_id']);
		#Organize Datas
		$vars = $vars + compact(
			'user', 'userDoc', 'cart', 'total', 'subTotal', 'creditCard',
			'tax', 'shippingCost', 'overShippingCost' ,'billingAddr', 'shippingAddr', 'discountExempt'
		);
		if ((!$cartEmpty) && (!empty($this->request->data['process'])) && ($total > 0)) {
			$order = Order::process($this->request->data, $cart, $vars, $avatax);
			if (empty($order->errors)) {
				#Redirect To Confirmation Page
				$this->redirect(array('Orders::view', 'args' => $order->order_id));
			}
		}
		#In car of credit card error redirect to the payment page
		if (Session::check('cc_error')){
			$this->redirect(array('Orders::payment'));
		}
		return $vars + compact('cartEmpty','order','cartByEvent','orderEvents','shipDate','savings');
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

	/**
	 * The user choose his billing address and enter his credit card information.
	 * - He can use a checkbox to apply shipping address as billing address
	 * - There is a jquery check for the credit card number
	 * @return compact
	 */
	public function payment() {
		$user = Session::read('userLogin');
		$fields = array(
			'item_id',
			'color',
			'category',
			'opt_description',
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
		#Prepare datas
		$cartExpirationDate = 0;
		$address = null;
		$payment = null;
		$checked = false;
		#Get billing address from shipping one in session
		$shipping = json_encode(Session::read('shipping'));
		#Check Datas Form
		if (!empty($this->request->data)) {
			$datas = $this->request->data;
			#Get Credit Card Infos
			if(!empty($datas['card_number'])) {
				#Get Only the card informations
				foreach($datas as $key => $value) {
					$card_array = explode("_", $key);
					if ($card_array[0] == 'card') {
						$card[$card_array[1]] = $value;
					}
				}
			}
			$cc_infos = CreditCard::create($card);
			#Check credits cards informations
			if($cc_infos->validates()) {
				#Encrypt CC Infos with mcrypt
				Session::write('cc_infos', Order::creditCardEncrypt($cc_infos, $user_id, true));
				$cc_passed = true;
			}
			#In case of normal submit (no ajax one with the checkbox)
			if(empty($datas['opt_shipping_select'])) {
				#Get Only address informations
				foreach($datas as $key => $data) {
					if (strlen(strstr($key,'card')) == 0 && strlen(strstr($key,'opt')) == 0) {
						$address_post[$key] = $data;
					}
				}
				$address = Address::create($address_post);
				#Check addresses informations
				if ($address->validates()) {
					Session::write('billing', $address->data());
					$billing_passed = true;
				}
			}
			#If both billing and credit card correct
			if(!empty($billing_passed) && !empty($cc_passed)) {
				$this->redirect(array('Orders::review'));
			}
			$data_add = array();
			if (!empty($address)) {
				$data_add = $address->data();
			}
			$payment = Address::create(array_merge($data_add,$card));
			#Init datas
			$payment->shipping_select = '0';
		}
		$cart = Cart::active(array(
				'fields' => $fields,
				'time' => '-5min'
		));
		foreach($cart as $item){
			if($cartExpirationDate < $item['expires']->sec) {
				$cartExpirationDate = $item['expires']->sec;
			}
		}
		$cartEmpty = ($cart->data()) ? false : true;
		if (Session::check('cc_error')){
			if (!isset($payment) || (isset($payment) && !is_object($payment))){
				$card = Order::creditCardDecrypt($user_id);
				$data_add = Session::read('cc_billingAddr');
				$payment = Address::create(array_merge($data_add,$card));
			}
			$payment->errors( $payment->errors() + array( 'cc_error' => Session::read('cc_error')));
			Session::delete('cc_error');
			Session::delete('cc_billingAddr');
		}
		return compact('address','cartEmpty','payment','shipping','cartExpirationDate');
	}

}

?>
