<?php

namespace app\models;

use MongoId;
use MongoDate;
use lithium\storage\Session;
use app\extensions\Mailer;
use app\models\User;
use app\models\Base;
use app\models\FeatureToggles;
use app\models\CreditCard;

use li3_payments\payments\TransactionResponse;
use li3_payments\extensions\adapter\payment\CyberSource;
use li3_payments\extensions\adapter\account\Customer;


class Order extends Base {

	protected static $_classes = array(
		'tax' => 'app\extensions\AvaTax',
		'payments' => 'li3_payments\payments\Processor'
	);

	protected $_dates = array(
		'now' => 0
	);

	public $validates = array(
		'authKey' => 'Could not secure payment.'
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	/**
	 * Produces a text summary of an order to be used in a menu.
	 *
	 * @param object $order
	 * @return string
	 */
	public function summary($order) {
		return array(
			(string) $order->order_id => $order->order_id.'- Order Total: $'.number_format($order->total, 2)
		);
	}

	/**
	 * Process all datas of the order and create an authorize.net transaction
	 *
	 * @see li3_payments\payments\Processor::authorize()
	 * @return object
	 */
	public static function process($data, $cart, $vars, $avatax) {
		$payments = static::$_classes['payments'];
		$userInfos = User::lookup($vars['user']['_id']);
		$order = static::create(array('_id' => new MongoId()));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		if ($cart) {
			#Get CreditCard
			$creditCard = $vars['creditCard'];
			#Switch Soft Authorized Amount Transaction depending of Credit Card Type
			if($creditCard['type'] == 'visa') {
				$authTotalAmount = 0;
			} else {
				$authTotalAmount = 1;
			}
			#If User has a CyberSource Profile, Use Token
			$auth = null;
			$cyberSourceProfile = User::hasCyberSourceProfile($userInfos['cyberSourceProfiles'], $creditCard);
			if(!empty($cyberSourceProfile)) {
				$cybersource = new CyberSource($payments::config('default'));
				$profile = $cybersource->profile($cyberSourceProfile['profileID']);
				if($profile instanceof Customer) {
					$auth = $payments::authorize('default', $authTotalAmount, $profile, array('orderID' => $order->order_id));
				}
			}
			#In Case No CyberSourceProfile has been found
			if(empty($auth)) {
				#Read Credit Card Informations
				$address = array(
					'firstName' =>  $vars['billingAddr']['firstname'],
					'lastName' => $vars['billingAddr']['lastname'],
					'address' => trim($vars['billingAddr']['address'] . ' ' . $vars['billingAddr']['address2']),
					'city' => $vars['billingAddr']['city'],
					'state' => $vars['billingAddr']['state'],
					'zip' => $vars['billingAddr']['zip'],
					'country' => $vars['billingAddr']['country'] ?: 'US',
					'email' =>  $vars['user']['email'] 
				);
				#Create Payment Object that contains Payment Informations
				$paymentInfos = $payments::create('default', 'creditCard', $creditCard + array(
					'billing' => $payments::create('default', 'address', $address)
					)
				);
				$auth = $payments::authorize('default', $authTotalAmount, $paymentInfos, array('orderID' => $order->order_id));
			}
			if (!$auth->success()) {
				#Reverse Transaction that Failed
				$payments::void('default', $auth, array(
					'processor' => $auth->adapter
				));
				Session::write('cc_error', implode('; ', $auth->errors));
				return false;
			}
			return static::recordOrder($vars, $cart, $order, $avatax, $auth, $items, $authTotalAmount, $creditCard);
		} else {
			$order->errors(
				$order->errors() + array($key => "All the items in your cart have expired. Please see our latest sales.")
			);
			$order->set($data);
			return false;
		}
	}

	/**
	 * Record in DB all informations linked with the order
	 * @return redirect
	 */
	public static function recordOrder($vars, $cart, $order, $avatax, TransactionResponse $auth, $items, $authTotalAmount, $creditCard) {
		$tax = static::$_classes['tax'];
		$user = Session::read('userLogin');
		$service = Session::read('services', array('name' => 'default'));
		#Update Items Status
		$inc = 0;
		foreach ($cart as $item) {
			$item['line_number'] = $inc;
			$item['status'] = 'Order Placed';
			$items[] = $item;
			++$inc;
		}
		#Save Credits Used
		if ($vars['cartCredit']->credit_amount) {
			User::applyCredit($user['_id'], $vars['cartCredit']->credit_amount);
			Credit::add(
				$vars['cartCredit'],
				$user['_id'],
				$vars['cartCredit']->credit_amount,
				"Used Credit",
				$order->order_id
			);
			Session::delete('credit');
			$order->credit_used = abs($vars['cartCredit']->credit_amount);
		}
		#Initialize Discount
		$vars['shippingCostDiscount'] = 0;
		$vars['overShippingCostDiscount'] = 0;
		#Save Promocode Used
		if ($vars['cartPromo']->saved_amount) {
			Promocode::add($vars['cartPromo']->code_id, $vars['cartPromo']->saved_amount, $vars['total']);
			$vars['cartPromo']->order_id = (string) $order->_id;
			$vars['cartPromo']->code_id = $vars['cartPromo']->code_id;
			$vars['cartPromo']->date_created = new MongoDate();
			$vars['cartPromo']->save();
			#If FreeShipping put Handling/OverSizeHandling to Zero
			if($vars['cartPromo']->type == 'free_shipping') {
				$vars['shippingCostDiscount'] = $vars['shippingCost'];
				$vars['overShippingCostDiscount'] = $vars['overShippingCost'];
			}
			#Update Order Information with PromoCode
			$order->promo_code = $vars['cartPromo']->code;
			$order->promo_type = $vars['cartPromo']->type;
			$order->promo_discount = abs($vars['cartPromo']->saved_amount);
		}
		#Save Services Used (10$Off / Free Shipping)
		if ($service) {
			$services = array();
			if (array_key_exists('freeshipping', $service) && $service['freeshipping'] === 'eligible') {
				$services = array_merge($services, array("freeshipping"));
				$vars['shippingCostDiscount'] = $vars['shippingCost'];
				$vars['overShippingCostDiscount'] = $vars['overShippingCost'];
				$order->discount = $vars['shippingCost'] + $vars['overShippingCost'];
			}
			if (array_key_exists('10off50', $service) && $service['10off50'] === 'eligible' && ($vars['subTotal'] >= 50.00)) {
				$order->discount = 10.00;
				$services = array_merge($services, array("10off50"));
			}
			if(!empty($services)) {
				$order->service = $services;
			}
		}
		#Save Tax Infos
		if($avatax === true){
			$tax::postTax(compact(
				'order', 'cartByEvent',
				'billingAddr',
				'shippingAddr', 'shippingCost', 'overShippingCost'
			));
		}
		#Shipping Method - By Default UPS
		$shippingMethod = 'ups';
		#Calculate savings
		$userSavings = Session::read('userSavings');
		$savings = $userSavings['items'] + $userSavings['discount'] + $userSavings['services'];	
		#Check in which case to store profile with token in Cybersource 
		$userInfos = User::lookup($user['_id']);
		#Get current credit cards to compare to this card
		if($userInfos['cyberSourceProfiles']) {
			$cyberSourceProfile = User::hasCyberSourceProfile($userInfos['cyberSourceProfiles'], $creditCard);
		}
		if(empty($cyberSourceProfile)) {
			$vars['savedByUser'] = false;
			$vars['order_id'] = $order->order_id;
			$vars['auth'] = $auth;
			$cyberSourceProfile = CreditCard::add($vars);
		}
		$order->cyberSourceProfileId = $cyberSourceProfile['profileID'];
		$cart = Cart::active();
		#Save Order Infos
		$shipDate = Cart::shipDate($cart);
		if($shipDate=="On or before 12/23"){
			$shipDateInsert = strtotime("2011-12-23".' +1 day');
		}
		elseif($shipDate=="See delivery alert below"){
			$shipDateInsert = Cart::shipDate($cart, true);
		}
		else{
			$shipDateInsert = $shipDate;
		}
		$order->save(array(
				'total' => $vars['total'],
				'subTotal' => $vars['subTotal'],
				'handling' => $vars['shippingCost'],
				'overSizeHandling' => $vars['overShippingCost'],
				'handlingDiscount' => $vars['shippingCostDiscount'],
				'overSizeHandlingDiscount' => $vars['overShippingCostDiscount'],
				'email' => $user['email'],
				'user_id' => (string) $user['_id'],
				'tax' => (float) $avatax['tax'],
				'card_type' => $creditCard['type'],
				'card_number' => substr($creditCard['number'], -4),
				'date_created' => static::dates('now'),
				/* The key is stored as `authKey` for BC and slow migration.
				   `auth` will contain key info, too. */
				'authKey' => $auth->key,
				'auth' => $auth->export(),
				'billing' => $vars['billingAddr'],
				'shipping' => $vars['shippingAddr'],
				'shippingMethod' => $shippingMethod,
				'items' => $items,
				'avatax' => $avatax,
				'ship_date' => new MongoDate($shipDateInsert),
				'savings' => $savings,
				'processor' => $auth->adapter,
				'authTotal' => $authTotalAmount
		));
		Cart::remove(array('session' => Session::key('default')));
		#Update quantity of items
		foreach ($cart as $item) {
			Item::sold($item->item_id, $item->size, $item->quantity);
		}
		#Update amount of user's orders
		$user = User::getUser();
		++$user->purchase_count;
		$user->save(null, array('validate' => false));
		#Send Order Confirmation Email
		
		$data = array(
			'order' => $order->data(),
			'shipDate' =>  date('m-d-Y', $shipDateInsert)
		);
		#In Case Of First Order, Send an Email About 10$ Off Discount
		if ($service && array_key_exists('freeshipping', $service) && $service['freeshipping'] === 'eligible') {
			Mailer::send('Welcome_10_Off', $user->email, $data);
		}
		Mailer::send('Order_Confirmation', $user->email, $data);
		#Clear Savings Information
		User::cleanSession();
		return $order;
	}

}

?>