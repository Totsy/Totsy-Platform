<?php

namespace app\tests\mocks\models;

class AffiliateMock extends \app\models\Affiliate {

	public static function getPixels($url, $invited_by) {
		return 'pixel';
	}

	public static function storeSubAffiliate($get_data, $affiliate) {
		return 'affiliate name';
	}

	public static function generatePixel($invited_by, $pixel, $options = array()) {
		return 'affiliate pixel';
    }

	public static function linkshareRaw($order, $tr, $entryTime, $trans_type) {
		return 'affiliate link raw';
    }

	public static function transaction($data, $affiliate, $orderid, $trans_type = 'new') {
		return true;
	}

	public static function linkshareCheck($userId, $affiliate, $cookie) {
		return true;
	}
}

?>