<?php

namespace app\models;

use MongoId;
use MongoDate;
use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\exceptions\TransactionException;

class Order extends \lithium\data\Model {

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
			(string) $order->_id => $order->total . ', ' . $order->items->{0}->description
		);
	}

	public function process($order, $user, $data, $cart) {
		foreach (array('billing', 'shipping') as $key) {
			$addr = $data[$key];
			${$key} = is_array($addr) ? Address::create($addr) : Address::first($addr);

			if (!${$key}->validates()) {
				$order->errors(
					$order->errors() + array($key => "Please use a valid {$key} address")
				);
			}
		}

		$card = Payments::create('default', 'creditCard', $data['card'] + array(
			'billing' => Payments::create('default', 'address', array(
				'firstName' => $billing->firstname,
				'lastName'  => $billing->lastname,
				'company'   => $billing->company,
				'address'   => trim($billing->address . ' ' . $billing->address_2),
				'city'      => $billing->city,
				'state'     => $billing->state,
				'zip'       => $billing->zip,
				'country'   => $billing->country
			))
		));
		$subTotal = array_sum($cart->subTotal());
		$tax = array_sum($cart->tax($shipping));
		$handling = Cart::shipping($cart, $shipping);

		// if (!$handling) {
		// 	$order->errors($order->errors() + array(
		// 		'shipping' => 'A valid shipping address was not specified.'
		// 	));
		// 	$order->set($data);
		// 	return false;
		// }

		$tax = $tax ? $tax + ($handling * Cart::TAX_RATE) : 0;
		$total = $subTotal + $tax + $handling;
		$cart = $cart->data();

		$inc = 0;
		foreach ($cart as $item) {
			$item['line_number'] = $inc;
			$item['status'] = 'Order Placed';
			$items[] = $item;
			++$inc;
		}
		try {
			return $order->save(compact('total', 'subTotal', 'tax', 'handling') + array(
				'user_id' => (string) $user['_id'],
				'card_type' => $card->type,
				'card_number' => substr($card->number, -4),
				'date_created' => static::dates('now'),
				'authKey' => Payments::authorize('default', $total, $card),
				'billing' => $billing->data(),
				'shipping' => $shipping->data(),
				'shippingMethod' => $data['shipping_method'],
				'giftMessage' => $data['gift-message'],
				'items' => $items
			));
		} catch (TransactionException $e) {
			$order->set($data);
			$order->errors($order->errors() + array($e->getMessage()));
		}
		return false;
	}
}

?>