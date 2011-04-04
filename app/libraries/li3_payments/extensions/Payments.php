<?php

namespace li3_payments\extensions;

use li3_payments\extensions\PaymentObject;

class Payments extends \lithium\core\Adaptable {

	/**
	 * Array of Adapter Configurations
	 *
	 * @var Array
	 */
	protected static $_configurations = array();
	
	/**
	 * Where to look for adapters
	 * 
	 * @var string
	 */
	protected static $_adapters = 'adapter.payments';

	/**
	 * The transaction classes 
	 *
	 * @var array
	 */
	protected static $_classes = array(
		'creditCard' => 'li3_payments\extensions\payments\CreditCard',
		'eCheck' => 'li3_payments\extensions\payments\ECheck',
		'customer' => 'li3_payments\extensions\payments\Customer',
		'address' => 'li3_payments\extensions\payments\Address',
		'profile' => 'li3_payments\extensions\payments\Profile'
	);

	/**
	 * Create a new payment object of a particular type, either a credit card or e-check.
	 *
	 * @param string $name
	 * @param string $type
	 * @param array $options
	 * @return void
	 * @todo Throw an exception if $type is invalid
	 */
	public static function create($name, $type, array $options = array()) {
		if (!isset(static::$_classes[$type])) {
			return null;
		}
		if (is_object($name)) {
			foreach (static::$_configurations as $key => $object) {
				if ($name == $object) {
					$name = $key;
					break;
				}
			}
		}
		$class = static::$_classes[$type];
		return new $class(array('connection' => $name) + $options);
	}

	/**
	 * undocumented function
	 *
	 * @param string $adapter 
	 * @return void
	 * @todo Figure out why this doesn't work
	 */
	protected static function _connectionName($adapter) {
		foreach (static::$_configurations as $key => $object) {
			if ($adapter == $object) {
				return $key;
			}
		}
	}

	public static function profile($name, $profile, array $options = array()) {
		return static::adapter($name)->profile($profile, $options);
	}

	public static function process($name, $amount, PaymentObject $pmt, array $options = array()) {
		return static::adapter($name)->process($amount, $pmt, $options);
	}

	public static function authorize($name, $amount, PaymentObject $pmt, array $options = array()) {
		return static::adapter($name)->authorize($amount, $pmt, $options);
	}

	public static function capture($name, $transaction, $amount, array $options = array()) {
		return static::adapter($name)->capture($transaction, $amount, $options);
	}

	public static function credit($name, $transaction, $amount = null, array $options = array()) {
		return static::adapter($name)->credit($transaction, $amount, $options);
	}

	public static function void($name, $transaction, array $options = array()) {
		return static::adapter($name)->void($transaction, $options);
	}

	public static function profiles($name, $query = null, array $options = array()) {
		return static::adapter($name)->read($query, $options);
	}
}

?>