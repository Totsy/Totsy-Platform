<?php

namespace app\models;

use MongoId;
use MongoDate;
use lithium\storage\Session;
use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\exceptions\TransactionException;
use app\extensions\Mailer;
use app\models\User;
use app\models\Base;
use app\models\FeatureToggles;

class Order extends Base {

	protected static $_classes = array(
		'tax' => 'app\extensions\AvaTax'
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
	 * @return object
	 */
	public static function process($data, $cart, $vars, $avatax) {
		$order = Order::create(array('_id' => new MongoId()));
		#Create Payment
		$card = Payments::create('default', 'creditCard', $vars['creditCard'] + array(
			'billing' => Payments::create('default', 'address', array(
				'firstName' => $vars['billingAddr']['firstname'],
				'lastName'  => $vars['billingAddr']['lastname'],
				'address'   => trim($vars['billingAddr']['address'] . ' ' . $vars['billingAddr']['address2']),
				'city'      => $vars['billingAddr']['city'],
				'state'     => $vars['billingAddr']['state'],
				'zip'       => $vars['billingAddr']['zip'],
				'country'   => $vars['billingAddr']['country']

			))
		));

		if ($cart) {
			$inc = 0;
			foreach ($cart as $item) {
				$item['line_number'] = $inc;
				$item['status'] = 'Order Placed';
				$items[] = $item;
				++$inc;
			}
			try {
				#Process Payment
				if ($vars['total'] > 0) {
					$authKey = Payments::authorize('default', $vars['total'], $card);
				} else {
					$authKey = Base::randomString(8,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
				}
				$order = Order::recordOrder($vars, $cart, $card, $order, $avatax, $authKey, $items);
				return $order;
			} catch (TransactionException $e) {
				Session::write('cc_error',$e->getMessage());
			}
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
	public static function recordOrder($vars, $cart, $card, $order, $avatax, $authKey, $items) {
			#Get User Informations
			$tax = static::$_classes['tax'];

			$user = Session::read('userLogin');
			$service = Session::read('services', array('name' => 'default'));
			$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
			#Save Credits Used
			if ($vars['cartCredit']->credit_amount) {
				User::applyCredit($user['_id'], $vars['cartCredit']->credit_amount);
				Credit::add($vars['cartCredit'], $user['_id'], $vars['cartCredit']->credit_amount, "Used Credit");
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
			#Get Credits Card Informations Encrypted and Store It
			$storing_cc_encrypted = FeatureToggles::getValue('storing_credit_card_encrypted');
			if(!empty($storing_cc_encrypted)) {
				$cc_encrypt = Session::read('cc_infos');
				$cc_encrypt['vi'] = Session::read('vi');
				unset($cc_encrypt['valid']);
				$order->cc_payment = $cc_encrypt;
			}

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
					'user_id' => (string) $user['_id'],
					'tax' => (float) $avatax['tax'],
					'card_type' => $card->type,
					'card_number' => substr($card->number, -4),
					'date_created' => static::dates('now'),
					'authKey' => $authKey,
					'billing' => $vars['billingAddr'],
					'shipping' => $vars['shippingAddr'],
					'shippingMethod' => $shippingMethod,
					'items' => $items,
					'avatax' => $avatax,
					'ship_date' => new MongoDate($shipDateInsert),
					'savings' => $savings
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
//				'shipDate' => date('M d, Y', Cart::shipDate($order))

			$data = array(
				'order' => $order->data(),
				'shipDate' => Cart::shipDate($order)
			);
			#In Case Of First Order, Send an Email About 10$ Off Discount
			if (array_key_exists('freeshipping', $service) && $service['freeshipping'] === 'eligible') {
				Mailer::send('Welcome_10_Off', $user->email, $data);
			}
			Mailer::send('Order_Confirmation', $user->email, $data);
			#Clear Savings Information
			User::cleanSession();
			return $order;
	}

	/**
	 * Decrypt credit card informations stored in the Session
	 */
	public static function creditCardDecrypt($user_id) {
		$cc_encrypt = Session::read('cc_infos');
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
 		$iv =  base64_decode(Session::read('vi'));
 		$key = md5($user_id);
		foreach	($cc_encrypt as $k => $cc_info) {
			$crypt_info = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key.sha1($k), base64_decode($cc_info), MCRYPT_MODE_CFB, $iv);
			$card[$k] = $crypt_info;
		}
		return $card;
	}

	/**
	 * Encrypt all credits card informations with MCRYPT and store it in the Session
	 */
	public static function creditCardEncrypt($cc_infos, $user_id,$save_iv_in_session = false) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		if ($save_iv_in_session == true) {
			Session::write('vi',base64_encode($iv));
		}
		$key = md5($user_id);
		foreach	($cc_infos as $k => $cc_info) {
			$crypt_info = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key.sha1($k), $cc_info, MCRYPT_MODE_CFB, $iv);
			$cc_encrypt[$k] = base64_encode($crypt_info);
		}
		return $cc_encrypt;
	}
}

?>