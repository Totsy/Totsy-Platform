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
	public function process($order, $total, $subTotal, $data, $cart, $orderCredit, $orderPromo, $tax, $handling, $overSizeHandling) {
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
		$cart = $cart->data();
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
				return $order->save(compact('total', 'subTotal','handling','overSizeHandling') + array(
					'user_id' => (string) $user['_id'],
					'tax' => (float) $tax,
					'card_type' => $card->type,
					'card_number' => substr($card->number, -4),
					'date_created' => static::dates('now'),
					'authKey' => $authKey,
					'billing' => $billingAddr->data(),
					'shipping' => $shippingAddr->data(),
					'shippingMethod' => $data['shipping_method'],
					'items' => $items
				));
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
}

?>