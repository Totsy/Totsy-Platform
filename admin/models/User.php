<?php

namespace admin\models;

use lithium\data\Connections;
use lithium\storage\Session;
use admin\models\Credit;
use MongoRegex;
use admin\models\Base;


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
	
	/**
	 * Create Unique Arrays using an md5 hash
	 *
	 * @param array $array
	 * @return array
	 */
	public static function arrayUnique($array, $preserveKeys = false)
	{
	    // Unique Array for return
	    $arrayRewrite = array();
	    // Array with the md5 hashes
	    $arrayHashes = array();
	    foreach($array as $key => $item) {
	        // Serialize the current element and create a md5 hash
	        $hash = md5(serialize($item));
	        // If the md5 didn't come up yet, add the element to
	        // to arrayRewrite, otherwise drop it
	        if (!isset($arrayHashes[$hash])) {
	            // Save the current element hash
	            $arrayHashes[$hash] = $hash;
	            // Add element to the unique Array
	            if ($preserveKeys) {
	                $arrayRewrite[$key] = $item;
	            } else {
	                $arrayRewrite[] = $item;
	            }
	        }
	    }
	    return $arrayRewrite;
	}
}

?>
