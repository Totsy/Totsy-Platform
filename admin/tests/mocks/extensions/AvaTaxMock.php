<?php

namespace admin\tests\mocks\extensions;

class AvaTaxMock extends \admin\extensions\AvaTax {

	public static function cancelTax($order, $tryNumber = 0) {
		return 0;
	}

	public static function getTax($data, $tryNumber = 0) {
		return array(
			'tax'=> 0,
			'avatax' => true
		);
	}

  	public static function postTax($data, $tryNumber = 0) {
		return 0;
	}

  	public static function returnTax($data, $tryNumber = 0) {
		return 0;
	}

	public static function commitTax($data, $tryNumber=0) {
		return 0;
	}
}

?>