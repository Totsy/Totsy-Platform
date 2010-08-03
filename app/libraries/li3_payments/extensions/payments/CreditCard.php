<?php

namespace li3_payments\extensions\payments;

class CreditCard extends \li3_payments\extensions\PaymentObject {

	/**
	 * Merchant-assigned key.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Credit card type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Credit card number.
	 *
	 * @var string
	 */
	public $number;

	/**
	 * The name of the card-holder.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * An integer value between 1 and 12 representing the month of the expiration date.
	 *
	 * @var integer
	 */
	public $month;

	/**
	 * An integer value representing the year of the expiration date.
	 *
	 * @var integer
	 */
	public $year;

	/**
	 * The CVV2 code for the credit card.
	 *
	 * @var string
	 */
	public $code;

	public $billing;

	protected $_validates = array(
		'type' => array('inList', 'list' => array()),
		'number' => array(array('creditCard', 'format' => 'any'), array('luhn')),
		'name' => array('notEmpty'),
		'code' => array('notEmpty'),
	);

	protected $_errors = array();

	public function __construct(array $config = array()) {
		$defaults = array(
			'type' => null,
			'number' => null,
			'name' => null,
			'month' => null,
			'year' => null,
			'code' => null,
		);
		parent::__construct($config + $defaults);
	}

	public function data() {
		$result = array();
		foreach (array('key', 'type', 'number', 'name', 'month', 'year', 'code') as $key) {
			$result[$key] = $this->{$key};
		}
		return $result;
	}

	protected function _init() {
		parent::_init();
		$validator = $this->_classes['validator'];
		$this->_validates['number'][0]['format'] = $this->type ?: 'any';
		$this->_validates['type']['list'] = array_keys($validator::rules('creditCard'));
	}
}

?>