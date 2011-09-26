<?php

namespace app\tests\mocks\models;

use lithium\storage\Session;

class OrderMock extends \app\models\Order {

	protected static $_classes = array(
		'tax' => 'app\tests\mocks\extensions\AvaTaxMock',
		'payments' => 'app\tests\mocks\extensions\PaymentsMock'
	);

	public static function creditCardDecrypt($user_id) {
		$cc_infos = Session::read('cc_infos');

		foreach ($cc_infos as $key => &$value) {
			$value = str_replace('encrypted:', '', $value);
		}
		return $cc_infos;
	}

	public static function creditCardEncrypt($cc_infos, $user_id, $save_iv_in_session = false) {
		foreach ($cc_infos as $key => &$value) {
			$value = 'encrypted:' . $value;
		}
		return $cc_infos;
	}
}

?>