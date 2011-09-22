<?php

namespace admin\tests\mocks\extensions;

use li3_payments\extensions\PaymentObject;

class PaymentsMock extends \li3_payments\extensions\Payments {

	public static $profile;

	public static $process;

	public static $authorize;

	public static $capture;

	public static $credit;

	public static $void;

	public static $profiles;

	public static function resetMock() {
		static::$profile   = null;
		static::$process   = null;
		static::$authorize = null;
		static::$capture   = null;
		static::$credit    = null;
		static::$void      = null;
		static::$profiles  = null;
	}

	public static function profile($name, $profile, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return true;
	}

	public static function process($name, $amount, PaymentObject $pmt, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return 'transaction id';
	}

	public static function authorize($name, $amount, PaymentObject $pmt, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return 'transaction id';
	}

	public static function capture($name, $transaction, $amount, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return 'transaction id';
	}

	public static function credit($name, $transaction, $amount = null, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return null;
	}

	public static function void($name, $transaction, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return 'transaction id';
	}

	public static function profiles($name, $query = null, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return null;
	}

}

?>