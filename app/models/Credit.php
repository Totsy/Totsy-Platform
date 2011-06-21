<?php

namespace app\models;

use MongoDate;
use lithium\storage\Session;
use lithium\util\Validator;

class Credit extends \lithium\data\Model {

	const INVITE_CREDIT = 15.00;

	protected $_dates = array(
		'now' => 0,
		'tenMinutes' => 600
	);

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	public static function add($credit, $user_id, $amount, $reason) {
		$credit->created = static::dates('now');
		$credit->user_id = (string) $user_id;
		$credit->credit_amount = $amount;
		$credit->reason = $reason;
		return static::_object()->save($credit);
	}

	public function checkCredit($entity, $credit_amount, $subTotal, $userDoc) {

		if (Session::read('credit')) {
			$entity->credit_amount = Session::read('credit');
		}

		if ($credit_amount) {
		    $entity->credit_amount = $credit_amount;
			$credit = number_format((float)$entity->credit_amount, 2);
			$lower = -0.999;
			$upper = (!empty($userDoc->total_credit)) ? $userDoc->total_credit + 0.01 : 0;
			$inRange = Validator::isInRange($credit, null, compact('lower', 'upper'));
			$isMoney = Validator::isMoney($credit);
			if (!$isMoney) {
				$entity->error = "Please apply credits that are in the form of $0.00";
				$entity->errors(
					$entity->errors() + array('amount' => "Please apply credits that are in the form of $0.00")
				);
			}
			if (!$inRange) {
				$entity->errors(
					$entity->errors() + array(
						'amount' => "Please apply credits that are greater than $0 and less than $$userDoc->total_credit"
					));
			}
			$isValid = ($subTotal >= $credit) ? true : false;
			if (!$isValid) {
				$entity->errors(
					$entity->errors() + array(
						'amount' => "Please apply credits that is $$subTotal or less"
					));
			}
			if ($isMoney && $inRange && $isValid) {
				$entity->credit_amount = -$credit;
				Session::write('credit', -$credit, array('name' => 'default'));
			}
		}
	}
}

?>