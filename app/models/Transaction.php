<?php

namespace app\models;

use li3_payments\extensions\Payments;

class Transaction extends \lithium\data\Model {

	public $validates = array(
		'authKey' => 'Could not secure payment.'
	);

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
		$total = $subTotal + $tax + $handling;

		return $transaction->save(compact('total', 'subTotal') + array(
			'authKey' => Payments::authorize('default', $total, $card),
			'billing' => $billing->data(),
			'shipping' => $shipping->data(),
			'shippingMethod' => $data['shipping_method'],
			'giftMessage' => $data['gift-message'],
			'items' => $cart->data()
		));
	}
}

?>