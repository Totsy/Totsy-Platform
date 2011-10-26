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

	public static function add($credit, $user_id, $amount, $reason, $orderid) {
		$credit->created = static::dates('now');
		$credit->user_id = (string) $user_id;
		$credit->credit_amount = $amount;
		$credit->order_id = $orderid;
		$credit->reason = $reason;
		return static::_object()->save($credit);
	}
	
	public function checkCredit($entity, $credit_amount, $subTotal, $userDoc) {

		if (Session::read('credit')) {
			$entity->credit_amount = Session::read('credit');
			if ($credit_amount == null) {
				$credit_amount = $entity->credit_amount;
			}
		}
		if ((float) $credit_amount >= 0.00) {
		    $entity->credit_amount = $credit_amount;
			$credit = (float) number_format((float)$credit_amount,2,'.','');
			$lower = -0.999;
			$upper = (!empty($userDoc->total_credit)) ? $userDoc->total_credit + 0.01 : 0;
			$inRange = Validator::isInRange($credit, null, compact('lower', 'upper'));
			/**$isMoney = Validator::isMoney((string) '$'.$credit);
			if (!$isMoney) {
				$entity->error = "Please apply credits that are in the form of $0.00";
				$entity->errors(
					$entity->errors() + array('amount' => "Please apply credits that are in the form of $0.00")
				);
			}**/
			if (!$inRange) {
				$errors = true;
				$entity->errors(
					$entity->errors() + array(
						'amount' => "Please apply credits that are greater than $0 and less than $$userDoc->total_credit"
					));
			}
			$isValid = ($subTotal >= $credit) ? true : false;
			if ($inRange && empty($errors)) {
			 	if($isValid) {
			 		$entity->credit_amount = -$credit;
					Session::write('credit', $credit, array('name' => 'default'));
			 	} else {
			 		$entity->credit_amount = -$subTotal;
					Session::write('credit', $subTotal, array('name' => 'default'));
			 	}
			} else {
				$entity->credit_amount = 0;
			}
		}
	}
}

?>