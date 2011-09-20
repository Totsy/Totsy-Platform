<?php

namespace app\tests\mocks\extensions;

class AvaTaxMock extends \app\extensions\AvaTax {

	public static function getTax($data, $tryNumber = 0) {
		return array(
			'tax' => 0,
			'avatax' => true
		);
	}

	public static function postTax($data, $tryNumber = 0) {
		return 0;
	}
}

?>