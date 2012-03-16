<?php

namespace app\controllers;

use app\models\User;
use app\models\Cart;
use app\models\Item;
use app\models\Credit;
use app\models\Address;
use app\models\Event;
use app\models\Affiliate;
use app\models\Promotion;
use app\models\CreditCard;
use app\models\Order;
use app\models\Promocode;
use app\models\OrderShipped;
use app\models\Service;
use app\controllers\BaseController;
use lithium\storage\Session;
use lithium\util\Validator;
use MongoDate;
use MongoId;
use app\extensions\Mailer;
use app\extensions\AvaTax;

/**
 * The Orders Controller
 *
 * @see http://admin.totsy.local/docs/admin/controllers/OrdersController
 **/
class OrdersController extends BaseController {

	protected $_classes = array(
		'tax'       => 'app\extensions\AvaTax',
		'order'     => 'app\models\Order',
		'creditCard' => 'app\models\CreditCard',
		'affiliate' => 'app\models\Affiliate'
	);

	/**
	 * Allows the view of all the orders a customer has in descending order.
	 * The ship date is also populated next to each order if applicable.
	 * @return compact
	 */
	public function index() {
		$orderClass = $this->_classes['order'];
		$user = Session::read('userLogin');

		$orders = $orderClass::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user['_id']),
			'order' => array('date_created' => 'DESC')
		));
		$shipDate = null;
		$trackingNumbers = array();
		$lifeTimeSavings = 0;

		foreach ($orders as $key => $order) {
			$list = $trackingNum = array();
			$shipDate["$order->_id"] = Cart::shipDate($order);
			$conditions = array('OrderId' => $order->_id);

			$shipRecords = OrderShipped::find('all', compact('conditions'));
			$trackingNum = array();
			foreach ($shipRecords as $record) {
				if (!in_array($record->{'Tracking #'}, $list)) {
					$list[] = $record->{'Tracking #'};
					$shipMethod = (empty($record->ShipMethod) ? 'UPS' : $record->ShipMethod);
					$trackingNum[] = array('code' => $record->{'Tracking #'}, 'method' => $shipMethod);
				}
			}
			#Get All Tracking Numbers for One Order
			if ($trackingNum) {
				$trackingNumbers["$order->_id"] = $trackingNum;
			}
			#Calculatings LifeTime Savings
			if (empty($order["cancel"])) {
				foreach ($order["items"] as $item) {
					$itemInfo = Item::find('first', array('conditions' => array("_id" => new MongoId($item["item_id"]))));
					if (empty($item->cancel)) {
						$lifeTimeSavings += ($item["quantity"] * ($itemInfo['msrp'] - $itemInfo['sale_retail']));
					}
				}
			}
		}if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_index';
		}
		
		return (compact('orders', 'shipDate', 'trackingNumbers', 'lifeTimeSavings'));
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
		$orderClass     = $this->_classes['order'];
		$affiliateClass = $this->_classes['affiliate'];

		$user = Session::read('userLogin');
		$order = $orderClass::find('first', $a =array(
			'conditions' => array(
				'order_id' => $order_id,
				'user_id' => (string) $user['_id']
		)));

		$new = ($order->date_created->sec > (time() - 120)) ? true : false;
		if($order->date_created->sec < 1322006400){
			$shipDate = Cart::shipDate($order, true);
			$shipDate = date('M d, Y', $shipDate);
		}
		else{
			$shipDate = Cart::shipDate($order);
		}
		if (!empty($shipDate)) {
			$allEventsClosed = (Cart::getLastEvent($order)->end_date->sec > time()) ? false : true;
		} else {
			$allEventsClosed = true;
		}
		$shipped = (isset($order->tracking_numbers)) ? true : false;
		$shipRecord = (isset($order->ship_records)) ? true : false;
		$preShipment = ($shipped || $shipRecord) ? true : false;
		$itemsByEvent = $this->_itemGroupByEvent($order);
		$orderEvents = $this->_orderEvents($order);
		//Check if all items from one event are closed
		foreach($itemsByEvent as $key => $items_e) {
			$url = Event::find('first', array('conditions' => array('_id'=> $key)));
			foreach($items_e as $key_b => $item) {
				if(empty($item['cancel'])) {
					$openEvent[$item['event_id']] = true;
				}

				$itemRecord = Item::find($item['item_id']);
				if (!empty($itemRecord)) {

					$itemsByEvent[$key][$key_b]['sku'] = $itemRecord->sku_details[$item['size']];
					$itemsToSend[] =  array(
										'id' => (string) $itemRecord['_id'],
										'qty' => $item['quantity'],
										'title' => $itemRecord['description'],
										'price' => $itemRecord['sale_retail']*100,
									 	'url' => 'http://'.$_SERVER['HTTP_HOST'].'/sale/'.$url->url.'/'.$itemRecord['url']
					);
					unset($itemRecord);
				}
			}
		}

		// IMPORTANT!
		// Sailthru purchase api complete
		if ($new===true){
			if ( !Session::check('order_'.$order_id,array('name'=>'default')) ){
				Mailer::purchase(
				$user['email'],
				$itemsToSend,
				array('message_id' => hash('sha256',Session::key('default').substr(strrev( (string) $user['_id']),0,8)))
				);
				Session::write('order_'.$order_id,time(),array('name'=>'default'));
			}

		}
		unset($itemsToSend);

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

			$itemInfo = Item::find('first', array('conditions' => array("_id" => new MongoId($item["item_id"]))));
			if (empty($item->cancel)) {
				$savings += $item["quantity"] * ($itemInfo['msrp'] - $itemInfo['sale_retail']);
			}
		}
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_view';
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
			'openEvent',
			'savings'
		);
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
		#Check Expires
		Cart::cleanExpiredEventItems();
		#Prepare datas
		$address = null;
		$selected = null;
		$cartExpirationDate = 0;
		$addresses_ddwn = array();
		$shipDate = null;
		$error = null;

		#Check Datas Form
		if (!empty($this->request->data)) {
			$datas = $this->request->data;
			#Check If the User want to save the current address
			if(!empty($datas['opt_save'])) {
				$save = true;
				unset($datas['opt_save']);
			}
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
					if ($count == 0 || !empty($save)) {
						$address->user_id = $user['_id'];
						$address->type = 'Shipping';
						$address->save();
					}
					return $this->redirect(array('Orders::payment'));
				} else {
					$error = true;
				}
			}
		}
		#Get all addresses of the current user
		$addresses = Address::all(array(
			'conditions' => array('user_id' => (string) $user['_id'], 'type' => 'Shipping')
		));
		#Prepare addresses datas for the dropdown
		if (!empty($addresses)) {
			$idx = 0;
			foreach ($addresses as $value) {
				if ((($idx == 0 || $value['default'] == '1') && empty($datas['address_id']))) {
					$address = $value;
				}
				#Get selected ddwn address
				if((string)$value['_id'] == $address['_id']) {
					$selected = (string) $value['_id'];
				}
				$addresses_ddwn[(string)$value['_id']] = $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['address'];
				$idx++;
			}
		}
		#Get Shipping Address from Session
		if (Session::read('shipping') && empty($datas['address_id'])) {
			$address = Address::create(Session::read('shipping'));
		}
		#Check Cart Validty
		$cart = Cart::active(array(
				'fields' => $fields,
				'time' => 'now'
		));
		$shipDate = Cart::shipDate($cart);
		foreach($cart as $item){

			if($cartExpirationDate < $item['expires']->sec) {
				$cartExpirationDate = $item['expires']->sec;
			}
		}
						
		$cartEmpty = ($cart->data()) ? false : true;
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_shipping';
		}
		return compact(
			'address',
			'addresses_ddwn',
			'shipDate',
			'cartEmpty',
			'error',
			'selected',
			'cartExpirationDate'
		);
	}

	/**
	 * Processes an order by capturing payment.
	 *
	 * @fixme `$cartExpirationDate` is undefined where should this be coming
	 +        from? Currently the comparison will always fail and expiration date
	 *        will be set to `$cartValue['expires']->sec`.
	 * @todo Improve documentation
	 * @return compact
	 */
	public function review() {
		$taxClass   = $this->_classes['tax'];
		$orderClass = $this->_classes['order'];
		$creditCardClass = $this->_classes['creditCard'];
		
		if (Session::check('cc_infos') || Session::check('CyberSourceProfile')) {
			$payment_info = true;
		} else {
			$payment_info = false;
		}
		
		#Check Users are in the correct step
		if (!Session::check('shipping')) {
			return $this->redirect(array('Orders::shipping'));
		}
		if (!Session::check('billing') || !$payment_info) {				
			return $this->redirect(array('Orders::payment'));
		}
		#Check Expires
		Cart::cleanExpiredEventItems();
		#Get Users Informations
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
			'event',
			'discount_exempt'
		);
		$promocode_disable = false;
		#Get Current Cart
		$cart = $taxCart = Cart::active(array('fields' => $fields, 'time' => 'now'));
		$cartEmpty = ($cart->data()) ? false : true;
		$cartByEvent = $this->_itemGroupByEvent($cart);
		#Calculate Shipped Date
		$shipDate = Cart::shipDate($cart);
		#Get Value Of Each and Sum It
		$subTotal = 0;
		$cartExpirationDate = 0;

		$i = 0;
		foreach ($cart as $cartValue) {
			#Get Last Expiration Date
			if ($cartExpirationDate < $cartValue['expires']->sec) {
				$cartExpirationDate = $cartValue['expires']->sec;
			}
			$event = Event::find('first', array(
				'conditions' => array('_id' => $cartValue->event[0])
			));
			$cartItemEventEndDates[$i] = is_object($event->end_date) ? $event->end_date->sec : $event->end_date;
			$cartValue->event_name = $event->name;
			$cartValue->event_url = $event->url;
			$cartValue->event_id = $cartValue->event[0];
			$subTotal += $cartValue->quantity * $cartValue->sale_retail;
			$i++;
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
		#Getting Tax by Avatax
		$avatax = $taxClass::getTax(compact(
			'cartByEvent', 'billingAddr', 'shippingAddr', 'shippingCost', 'overShippingCost',
			'orderCredit', 'orderPromo', 'orderServiceCredit', 'taxCart'));
		$tax = (float) $avatax['tax'];
		if(Cart::isOnlyDigital($cart)) {
			$avatax = null;
			$tax = 0.00;
		}
		#Get current Discount
		$cartDiscount = Cart::active();
		$vars = Cart::getDiscount($cartDiscount, $subTotal, $shippingCost, $overShippingCost, $this->request->data, $tax);
				
		#Calculate savings
		$userSavings = Session::read('userSavings');
		$savings = $userSavings['items'] + $userSavings['discount'] + $userSavings['services'];
		#Get Credits
		if (!empty($vars['cartCredit'])) {
			$credits = Session::read('credit');
		}
		#Get Services
		$services = $vars['services'];
		
		#Get Discount Freeshipping Service / Get Discount Promocodes Free Shipping
		if((!empty($services['freeshipping']['enable'])) || ($vars['cartPromo']['type'] === 'free_shipping')) {
			if(!Cart::isOnlyDigital($cart)) {
				$shipping_discount = 7.95;
			} else {
				$shipping_discount = 0.00;
			}
		}
				
		#Calculate Order Total
		$total = round(floatval($vars['postDiscountTotal']), 2);
		#Read Credit Card Informations
		if (Session::check('cc_infos')) {
			$creditCard = $creditCardClass::decrypt((string)$user['_id']);
		} else if (Session::check('CyberSourceProfile')) {
			$user = Session::read('userLogin');
			$userInfos = User::lookup($user['_id']);
		
			$creditCard_profileId = Session::read('CyberSourceProfile');
			
			foreach($userInfos['cyberSourceProfiles'] as $cyberSourceProfile) {
				if($cyberSourceProfile['profileID'] == $creditCard_profileId) {
					$selectedCyberSourceProfile = $cyberSourceProfile->data();
				}
			}					
			
			$creditCard = $selectedCyberSourceProfile[creditCard];
		}
		#Organize Datas
		$vars = $vars + compact(
			'user', 'cart', 'total', 'subTotal',
			'tax', 'shippingCost', 'overShippingCost' ,'billingAddr', 'shippingAddr', 'shipping_discount','creditCard'
		);
		
		if ((!$cartEmpty) && (!empty($this->request->data['process']))) {

			/* Process this order and run it through the payment processor. */
			$order = $orderClass::process($this->request->data, $cart, $vars, $avatax);

			if (empty($order->errors) && !(Session::check('cc_error'))) {
				#Redirect To Confirmation Page
				return $this->redirect(array('Orders::view', 'args' => $order->order_id));
			}
		}
		#In car of credit card error redirect to the payment page
		if (Session::check('cc_error')) {
			return $this->redirect(array('Orders::payment'));
		}
		#Check if Services
		$serviceAvailable = false;
		if(Session::check('service_available')) {
			$serviceAvailable = Session::read('service_available');
		}
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_review';
		}
		
		return $vars + compact(
			'cartEmpty',
			'order',
			'shipDate',
			'savings',
			'credits',
			'services',
			'cartExpirationDate',
			'promocode_disable',
			'serviceAvailable',
			'cartItemEventEndDates'
		);
	}

	/**
	 * The user choose his billing address and enter his credit card information.
	 * - He can use a checkbox to apply shipping address as billing address
	 * - There is a jquery check for the credit card number
	 * @return compact
	 */
	public function payment() {		
		$orderClass = $this->_classes['order'];
		$creditCardClass = $this->_classes['creditCard'];

		#Check Users are in the correct step
		if (!Session::check('shipping')) {
			return $this->redirect(array('Orders::shipping'));
		}
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
		
		#Check Expires
		Cart::cleanExpiredEventItems();
		#Prepare datas
		$cartExpirationDate = 0;

		$address = null;
		$payment = null;
		$checked = false;
		$card = array();
		$addresses_ddwn = array();

		#Get billing address from shipping one in session
		$shipping = json_encode(Session::read('shipping'));

		#Get Billing Address from Session
		if (Session::read('billing')) {
			$payment = Address::create(Session::read('billing'));
		}

		#Get CyberSourceProfiles recorded for this user
		$userInfos = User::lookup($user['_id']);
		$cyberSourceProfiles = array();
		if($userInfos['cyberSourceProfiles']) {
			$cyberSourceProfiles = $userInfos['cyberSourceProfiles'];
		}
		
		#Check Datas Form
		if (!empty($this->request->data)) {
			$datas = $this->request->data;
												
			#Check If the User want to save the current address
			if($datas['paymentInfosSave']) {
				$save = true;
			}
			//if the user selected a saved credit card, than prepopulate the relevant fields to go to the order review page

			if ($datas['savedCreditCard'] && !$save) {
				$creditCard_profileId = $datas['savedCreditCard'];

				foreach($userInfos['cyberSourceProfiles'] as $cyberSourceProfile) {
					if($cyberSourceProfile['profileID'] == $creditCard_profileId) {
						Session::write('CyberSourceProfile', $creditCard_profileId);
						$selectedCyberSourceProfile = $cyberSourceProfile->data();
						Session::write('billing', $selectedCyberSourceProfile['billing']);
					}
				}				
												
				$cc_passed = true;
				$billing_passed = true;
				#Remove Credit Card Errors
				Session::delete('cc_error');
			} else { 	
				if($save) {
					$creditCard[type] = $datas['card_type'];
					$creditCard[number] = $datas['card_number'];
					$creditCard[year] = $datas['card_year'];
					$creditCard[month] = $datas['card_month'];
					$creditCard[code] = $datas['card_code'];
	
					$vars['billingAddr']['firstname'] = $datas[firstname];
					$vars['billingAddr']['lastname'] = $datas[lastname];
					$vars['billingAddr']['address'] = $datas[address];
					$vars['billingAddr']['address2'] = $datas[address2];
					$vars['billingAddr']['city'] = $datas[city];
					$vars['billingAddr']['state'] = $datas[state];
					$vars['billingAddr']['zip'] = $datas[zip];
					$vars['user'] = $user;
					$vars['creditCard'] = $creditCard;
					$vars['savedByUser'] = true;
				
				 	$creditCardClass::add($vars);
				}
	
				if (!empty($datas['address_id'])) {
					$address = Address::first(array(
						'conditions' => array('_id' => new MongoId($datas['address_id'])
					)));
				}
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
				$cc_infos = $creditCardClass::create($card);
				#Check credits cards informations
				if($cc_infos->validates()) {
					#Encrypt CC Infos with mcrypt
					Session::write('cc_infos', $creditCardClass::encrypt($cc_infos, (string)$user['_id'], true));
					$cc_passed = true;
					#Remove Credit Card Errors
					Session::delete('cc_error');
				}
				
				#In case of normal submit (no ajax one with the checkbox)
				if(empty($datas['opt_shipping_select']) && empty($datas['address_id'])) {
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
						if (!empty($save)) {
							$address->user_id = $user['_id'];
							$address->type = 'Billing';
							unset($address->paymentInfosSave); //remove the payment info saved flag on the billing address storage.
							$address->save();
						}
					}
				}
			}
			#If both billing and credit card correct
			if(!empty($billing_passed) && !empty($cc_passed)) {
				return $this->redirect(array('Orders::review'));
			}
			$data_add = array();
			if (!empty($address)) {
				if(is_array($address)) {
					$data_add = $addres;
				} else {
					$data_add = $address->data();
				}
			}
			$payment = Address::create(array_merge($data_add,$card));
			#Init datas
			$payment->shipping_select = '0';
		} //END OF POST / REQUEST DATA
		
		
		#Get all addresses of the current user
		$addresses = Address::all(array(
			'conditions' => array('user_id' => (string) $user['_id'], 'type' => 'Billing')
		));
		#Prepare addresses datas for the dropdown
		if (!empty($addresses)) {
			$idx = 0;
			foreach ($addresses as $value) {
				if ((($idx == 0 || $value['default'] == '1') && empty($datas['address_id']))) {
					$address = $value;
				}
				foreach($value as $key => $addressInfo) {
					$billingAddresses[(string)$value['_id']][$key] = $addressInfo;
				}
				
				$addresses_ddwn[(string)$value['_id']] = $value['firstname'] . ' ' . $value['lastname'] . ' ' . $value['address'];
				$idx++;
			}
		}
		$cart = Cart::active(array(
				'fields' => $fields,
				'time' => '-5min'
		));
		$shipDate = Cart::shipDate($cart);
		foreach($cart as $item){

			if($cartExpirationDate < $item['expires']->sec) {
				$cartExpirationDate = $item['expires']->sec;
			}
		}
		$cartEmpty = ($cart->data()) ? false : true;
		if (Session::check('cc_error')){
			$creditCardError = true;
			if (!isset($payment) || (isset($payment) && !is_object($payment))){
				$card = $creditCardClass::decrypt((string)$user['_id']);
				$data_add = Session::read('billing');
				$payment = Address::create(array_merge($data_add,$card));
			}
			
			
			//error handling is not properly done
			//errors from cybersource are not description or consumer-friendly
			//errors need to be captured and then re-worded for users
			//for now we have hardcoded a generic error message for all cc_errors stored in Session
			
			
			//$payment->errors( $payment->errors() + array( 'cc_error' => Session::read('cc_error')));
			$ccErrorTextGeneric = "We are not able to charge this credit card.  Please verify that your credit card number, expiration date, and security code are valid, or try another card.";
			$payment->errors(array( 'cc_error' => $ccErrorTextGeneric));
			Session::delete('cc_error');
			Session::delete('billing');
		}
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_payment';
		}
		
		
		$saved_by_user = 0;
		foreach($cyberSourceProfiles as $cyberSourceProfile) {
			if ($cyberSourceProfile[savedByUser]) {
				$saved_by_user++;
			}
		}
		if ($saved_by_user == 0) {
			$cyberSourceProfiles = array();			
		}
		
		return compact(
			'billingAddresses',
			'address',
			'addresses_ddwn',
			'cartEmpty',
			'payment',
			'shipping',
			'shipDate',
			'cartExpirationDate',
			'cyberSourceProfiles',
			'creditCardError'
		);
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
	protected function _itemGroupByEvent($object) {
		$eventItems = array();

		if ($object) {
			$model = $object->model();

			if (strpos($model, 'models\Order') !== false) {
				foreach ($object->items->data() as $item) {
					$eventItems[$item['event_id']][] = $item;
				}
			}
			if ($model == 'app\models\Cart') {
				foreach ($object->data() as $item) {
					#Define If the CartItem is tangible
					if(Item::isTangible(new MongoId($item['item_id']))) {
						$item['tangible'] = true;
					} else {
						$item['tangible'] = false;
					}
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
	protected function _orderEvents($object) {
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
}

?>
