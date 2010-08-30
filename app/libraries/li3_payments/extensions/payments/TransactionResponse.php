<?php

namespace li3_payments\extensions\payments;

class TransactionResponse extends \lithium\core\Object {

	/**
	 * The primary transaction key or ID that may be used to reference this transaction in future
	 * operations.
	 *
	 * @var string
	 */
	protected $_key;

	/**
	 * A two-dimensional array of errors.
	 *
	 * @var array
	 */
	protected $_errors = array();

	protected $_responseCode;

	protected $_messages = array();

	protected $_autoConfig = array(
		'errors', 'responseCode', 'messages'
	);

	public function __get($name) {
		return isset($this->{"_$name"}) ? $this->{"_$name"} : null;
	}
}

?>