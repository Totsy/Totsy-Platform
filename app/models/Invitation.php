<?php

namespace app\models;

use lithium\storage\Session;
use MongoDate;

class Invitation extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0
	);
	
	public static function collection() {
		return static::_connection()->connection->invitations;
	}
	
	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}
	
	public static function add($invitation, $id, $email) {
		$invitation->user_id = $id;
		$invitation->date_sent = static::dates('now');
		$invitation->email = $email;
		$invitation->status = 'Sent';
		return static::_object()->save($invitation);
	}

	public function reject($winner, $email) {
		return static::collection()->update(array(
			'email' => $email, 
			'$ne' => $winner), 
			array('$set' => array(
				'status' => 'Ignored', 
				'date_updated' => static::dates('now')
		)));

	}
}