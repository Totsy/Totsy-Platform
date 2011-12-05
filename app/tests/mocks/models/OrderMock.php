<?php

namespace app\tests\mocks\models;

use lithium\storage\Session;

class OrderMock extends \app\models\Order {

	protected static $_classes = array(
		'tax' => 'app\tests\mocks\extensions\AvaTaxMock',
		'payments' => 'app\tests\mocks\extensions\PaymentsMock'
	);

	public static function creditCardDecrypt($user_id) {
		$data = Session::read('cc_infos');
		$result = array();

		foreach ($data as $key => $value) {
			$result[$key] = str_replace('encrypted:', '', $value);
		}
		return $result;
	}

	public static function creditCardEncrypt($cc_infos, $user_id, $save_iv_in_session = false) {
		$result = array();

		foreach ($cc_infos as $key => $value) {
			$result[$key] = 'encrypted:' . $value;
		}
		return $result;
	}
}

?>