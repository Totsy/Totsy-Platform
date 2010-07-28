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

	public function process($transaction, $user, $data, $cart) {
		foreach (array('billing', 'shipping') as $key) {
			$addr = $data[$key];
			${$key} = is_array($addr) ? Address::create($addr) : Address::first($addr);
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

		$subTotal = array_sum($cart->map(function($item) { return $item->sale_retail; })->data());
		$handling = Cart::shipping($cart, $shipping);
		$tax = array_sum($cart->tax($shipping));

		if ($tax && $handling) {
			$tax += ($handling * Cart::TAX_RATE);
		}
		$total = $subTotal + $tax + $handling;
		$cart = $cart->data();

		$inc = 0;
		foreach ($cart as $item) {
			$item['line_number'] = $inc;
			unset($item['created']);
			unset($item['expires']);
			unset($item['primary_images']);
			$items[] = $item;
			++$inc;
		}
		try {
			return $transaction->save(compact('total', 'subTotal', 'tax', 'handling') + array(
				'user_id' => new MongoId((string) $user['_id']),
				'date_created' => static::dates('now'),
				'authKey' => Payments::authorize('default', $total, $card),
				'billing' => $billing->data(),
				'shipping' => $shipping->data(),
				'shippingMethod' => $data['shipping_method'],
				'giftMessage' => $data['gift-message'],
				'items' => $items
			));
		} catch (TransactionException $e) {
			$transaction->errors(array($e->getMessage()));
		}
		return false;
	}
}

?>