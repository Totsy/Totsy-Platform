<?php

namespace li3_payments\extensions\payments;

class Customer extends \li3_payments\extensions\PaymentObject {

	/**
	 * Reference key assigned by the transaction processor.
	 *
	 * @var string
	 */
	protected $_key;

	/**
	 * Internal application-specific ID. Usually a reference to a record or document in a database.
	 *
	 * @var string
	 */
	protected $_id;

	protected $_type = 'individual'; // or 'business'

	protected $_description;

	protected $_firstName;

	protected $_lastName;

	protected $_email;

	protected $_payment;

	protected $_address;

	protected $_billing;

	protected $_shipping;

	protected $_autoConfig = array(
		'key', 'id', 'type', 'description', 'firstName', 'lastName', 'connection',
		'email', 'payment', 'address', 'billing', 'shipping', 'classes' => 'merge'
	);

	protected $_classes = array(
		'validator' => 'lithium\util\Validator',
		'payments' => 'li3_payments\extensions\Payments'
	);

	protected function _init() {
		parent::_init();
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
	}

	public function __get($name) {
		$name = "_{$name}";
		return isset($this->{$name}) ? $this->{$name} : null;
	}

	public function __isset($name) {
		$name = "_{$name}";
		return isset($this->{$name});
	}
}

?>