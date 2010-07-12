<?php

namespace li3_payments\extensions\payments;

class Customer extends \li3_payments\extensions\PaymentObject {

	/**
	 * Reference key assigned by the transaction processor.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Internal application-specific ID. Usually a reference to a record or document in a database.
	 *
	 * @var string
	 */
	public $id;

	public $type = 'individual'; // or 'business'

	public $description;

	public $firstName;

	public $lastName;

	public $email;

	public $payment;

	public $address;

	public $billing;

	public $shipping;

	protected function _init() {
		$payments = $this->_classes['payments'];
		$name = array(
			'firstName' => isset($this->_config['firstName']) ? $this->_config['firstName'] : '',
			'lastName' => isset($this->_config['lastName']) ? $this->_config['lastName'] : '',
		);

		foreach (array('address', 'billing', 'shipping') as $key) {
			if (isset($this->_config[$key]) && is_array($this->_config[$key])) {
				$this->_config[$key] = $payments::create(
					$this->_config['connection'],
					'address',
					$this->_config[$key] + $name
				);
			}
		}
		parent::_init();
	}
}

?>