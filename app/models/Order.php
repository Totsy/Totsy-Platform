<?php

namespace app\models;

use MongoId;
use MongoDate;
use lithium\storage\Session;
use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\exceptions\TransactionException;

class Order extends Base {

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
	public static function process($order, $total, $subTotal, $data, $cart, $vars, $avatax, $handling, $overSizeHandling) {
		$user = Session::read('userLogin');
		#Read Credit Card Informations
		$cc_encrypt = Session::read('cc_infos');
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
 		$iv =  base64_decode(Session::read('vi'));
 		$key = md5($user['_id']);
		foreach	($cc_encrypt as $k => $cc_info) {
			$crypt_info = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key.sha1($k), base64_decode($cc_info), MCRYPT_MODE_CFB, $iv);
			$card[$key] = $crypt_info;
		}
		#Create Payment
		$card = Payments::create('default', 'creditCard', $card + array(
			'billing' => Payments::create('default', 'address', array(
				'firstName' => $billingAddr->firstname,
				'lastName'  => $billingAddr->lastname,
				'company'   => $billingAddr->company,
				'address'   => trim($billingAddr->address . ' ' . $billingAddr->address_2),
				'city'      => $billingAddr->city,
				'state'     => $billingAddr->state,
				'zip'       => $billingAddr->zip,
				'country'   => $billingAddr->country
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
				if ($total > 0) {
					#Process Payment
					$authKey = Payments::authorize('default', $total, $card);
				} else {
					$authKey = $this->randomString(8,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
				}
				Order::recordOrder($vars, $order,$avatax);
			} catch (TransactionException $e) {
				$order->set($data);
				$order->errors($order->errors() + array($e->getMessage()));
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
	public static function recordOrder($vars, $order, $avatax) {
			#Save Order Infos
			$order->save(compact('total', 'subTotal','handling','overSizeHandling') + array(
					'user_id' => (string) $user['_id'],
					'tax' => (float) $avatax['tax'],
					'card_type' => $card->type,
					'card_number' => substr($card->number, -4),
					'date_created' => static::dates('now'),
					'authKey' => $authKey,
					'billing' => $billingAddr->data(),
					'shipping' => $shippingAddr->data(),
					'shippingMethod' => $data['shipping_method'],
					'items' => $items
			));
			$service = Session::read('services', array('name' => 'default'));
			$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
			#Save Credits Used
			if ($vars['cartCredit']->credit_amount) {
				User::applyCredit($user['_id'], $vars['cartCredit']->credit_amount);
				Credit::add($vars['cartCredit'], $user['_id'], $vars['cartCredit']->credit_amount, "Used Credit");
				Session::delete('credit');
				$order->credit_used = $vars['cartCredit']->credit_amount;
			}
			#Save Promocode Used
			if ($vars['cartPromo']->saved_amount) {
				Promocode::add((string) $code->_id, $vars['cartPromo']->saved_amount, $order->total);
				$vars['cartPromo']->order_id = (string) $order->_id;
				$vars['cartPromo']->code_id = (string) $code->_id;
				$vars['cartPromo']->date_created = new MongoDate();
				$vars['cartPromo']->save();
				$order->promo_code = $vars['cartPromo']->code;
				$order->promo_discount = $vars['cartPromo']->saved_amount;
			}
			#Save Services Used (10$Off / Free Shipping)
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
			#Save Tax Infos
			if($avatax === true){
				AvaTax::postTax( compact('order','cartByEvent', 'billingAddr', 'shippingAddr', 'shippingCost', 'overShippingCost') );
			}
			$order->avatax = $avatax;
			$order->ship_date = new MongoDate(Cart::shipDate($order));
			$order->save();
			Cart::remove(array('session' => Session::key('default')));
			#Clear Savings Information
			Session::delete('userSavings');
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
				'shipDate' => date('M d, Y', $shipDate)
			);	
			Mailer::send('Order_Confirmation', $user->email, $data);
			#In Case Of First Order, Send an Email About 10$ Off Discount
			if (array_key_exists('freeshipping', $service) && $service['freeshipping'] === 'eligible') {
				Mailer::send('Welcome_10_Off', $user->email, $data);
			}
			#Redirect To Confirmation Page
			return $this->redirect(array('Orders::view', 'args' => $order->order_id));
	}
}

?>