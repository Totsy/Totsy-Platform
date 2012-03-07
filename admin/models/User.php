<?php

namespace admin\models;

use lithium\data\Connections;
use lithium\storage\Session;
use admin\models\Credit;
use MongoRegex;
use MongoId;
use admin\models\Base;
use lithium\util\Validator;

class User extends Base {

	protected $_meta = array('source' => 'users');

	public static function applyCredit($data, $options = array()) {
		$options['type'] = empty($options['type']) ? null : $options['type'];
		$user = User::find('first', array(
			'conditions' => array(
				'_id' => $data['user_id']
		)));
		if ($user) {
			if ($options['type'] == 'Invite') {
				$amount = Credit::INVITE_CREDIT;
			} else {
				$amount = $data['sign'].$data['amount'];
			}
			if (empty($user->total_credit)) {
				$user->total_credit = $amount;
			} else {
				$user->total_credit = $user->total_credit + $amount;
			}
			$user->save(null, array('validate' => false));
		}
		return true;
	}

	public static function findUsers($data) {
		$exclude = array('address_type', 'type');
		foreach ($data as $key => $value) {
			if (($value != '') && (!in_array($key, $exclude))) {
				$conditions["$key"] = new MongoRegex("/$value/i");
			}
		}
		return static::find('all', array('conditions' => $conditions));
	}


	public static function lookupUserInvitedBy($user_id) {
	    if (empty($user_id)) return "";

	    Validator::add('mongoId', function($value) {
			return (strlen($value) >=10) ? true : false;
		});

		if (Validator::isMongoId($user_id)) {
		    $user_id = new MongoId($user_id);
		}

	    $result = static::collection()->findOne(array("_id" => $user_id), array(
	        '_id' => false,
	        'invited_by' => true
	    ));

	    return $result['invited_by'];
	}
    /**
	 * The lookup method takes the email address or id to search and finds
	 * the matching user.
	 *
	 * @param string $searchby - email or id
	 */
	public static function lookup($searchBy) {
		$user = null;

		 Validator::add('mongoId', function($value) {
			return (strlen($value) >=10) ? true : false;
		});
		if (Validator::isEmail($searchBy)) {
		    $searchBy = strtolower($searchBy);
			 $condition = array('email' => $searchBy);
		} else if (Validator::isMongoId($searchBy)) {
			$condition = array('_id' => new MongoId($searchBy));
		} else {
			$condition = array('_id' => $searchBy);
		}
		$result = static::collection()->findOne($condition);
		if ($result) {
			$user = User::create($result);
		}
		return $user;
	}
}


?>
