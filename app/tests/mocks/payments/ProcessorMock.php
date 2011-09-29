<?php

namespace app\tests\mocks\payments;

use li3_payments\payments\Account;
use li3_payments\payments\TransactionResponse;

class ProcessorMock extends \li3_payments\payments\Processor {

	public static $run;

	public static $profile;

	public static $process;

	public static $authorize;

	public static $capture;

	public static $credit;

	public static $void;

	public static $profiles;

	public static function resetMock() {
		static::$run       = null;
		static::$profile   = null;
		static::$process   = null;
		static::$authorize = null;
		static::$capture   = null;
		static::$credit    = null;
		static::$void      = null;
		static::$profiles  = null;
	}

	public static function run($name, $type, $transaction, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();
	}

	public static function profile($name, $profile, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();
	}

	public static function process($name, $amount, Account $pmt, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return new TransactionResponse(array(
			'key' => 'transaction id',
			'success' => true
		));
	}

	public static function authorize($name, $amount, Account $pmt, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return new TransactionResponse(array(
			'key' => 'transaction id',
			'success' => true
		));
	}

	public static function capture($name, $transaction, $amount, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return new TransactionResponse(array(
			'key' => 'transaction id',
			'success' => true
		));
	}

	public static function credit($name, $transaction, $amount = null, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();
	}

	public static function void($name, $transaction, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();

		return new TransactionResponse(array(
			'key' => 'transaction id',
			'success' => true
		));
	}

	public static function profiles($name, $query = null, array $options = array()) {
		$name = __FUNCTION__;
		static::${$name} = func_get_args();
	}
}

?>