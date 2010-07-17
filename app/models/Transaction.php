<?php

namespace app\models;

use li3_payments\extensions\Payments;

class Transaction extends \lithium\data\Model {

	public $validates = array();

	public function process($transaction, $user, $data, $cart, $addresses) {
	}
}

?>