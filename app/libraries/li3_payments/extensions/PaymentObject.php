<?php

namespace li3_payments\extensions;

abstract class PaymentObject extends \lithium\core\Object {

	protected $_autoConfig = array('classes' => 'merge', 'connection');

	protected $_classes = array(
		'validator' => 'lithium\util\Validator',
		'payments' => 'li3_payments\extensions\Payments'
	);

	protected $_connection;

	protected function _init() {
		parent::_init();
		$valid = array_keys(get_object_vars($this));

		foreach ($this->_config as $key => $value) {
			if ($value && in_array($key, $valid)) {
				$this->{$key} = $value;
			}
		}
	}

	public function validates() {
		$validator = $this->_classes['validator'];
		$this->_errors = $validator::check((array) $this, $this->_validates);
		return empty($this->_errors);
	}

	public function errors() {
		return $this->_errors;
	}

	public function set(array $values) {
		$valid = array_keys(get_object_vars($this));

		foreach ($values as $key => $val) {
			if (!in_array($key, $valid)) {
				continue;
			}
			
		}
	}

	public function save(array $values = array()) {
		if ($values) {
			$this->set($values);
		}
		if (!$this->_connection) {
			return false;
		}
		$payments = $this->_classes['payments'];
		$conn = $this->_connection;
		$connection = is_object($conn) ? $conn : $payments::adapter($conn);
		return $connection->profile($this);
	}

	public function delete() {
		if (!$this->_connection) {
			return false;
		}
		$payments = $this->_classes['payments'];
		$conn = $this->_connection;
		$connection = is_object($conn) ? $conn : $payments::adapter($conn);
		return $connection->delete($this);
	}
}

?>