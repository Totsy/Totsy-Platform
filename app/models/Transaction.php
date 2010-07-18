<?php

namespace app\models;

use li3_payments\extensions\Payments;

class Transaction extends \lithium\data\Model {

	public $validates = array();

	public function process($transaction, $user, $data, $cart, $addresses) {
		$ids = array_keys($addresses);
		$addresses = array_map(
			function($id) { return Address::first($id); }, array_combine($ids, $ids)
		);
		$billing = $addresses[$data['billing_address']];

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
		$amount = 0;
		$manifest = array();

		foreach ($data['shipping'] as $id => $address) {
			if (!isset($manifest[$address])) {
				$manifest[$address] = $addresses[$address]->data() + array('items' => array());
			}
			foreach ($cart as $item) {
				if ((string) $item->_id == $id) {
					$manifest[$address]['items'][] = array(
						'description' => $item->description,
						'color' => $item->color,
						'size' => $item->size,
						'price' => $item->sale_retail,
						'quantity' => $item->quantity
					);
					break;
				}
			}
		}
		$cart->each(function($item) use (&$amount) { $amount += $item->sale_retail; });

		// @todo Tax-calculation rules go here.
		$transaction->key = Payments::process('default', $amount, $card);
		$transaction->manifest = array_values($manifest);
		return $transaction->save();
	}
}

?>