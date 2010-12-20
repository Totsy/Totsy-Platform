<?php

namespace admin\models;

use lithium\storage\Session as LiSession;

class Promocode extends \lithium\data\Model {
    
	public static function createdBy($data){
		$user = LiSession::read('userLogin');
		$data['created_by']= $user['_id'];
		
		return $data;
	}
	    
}


?>