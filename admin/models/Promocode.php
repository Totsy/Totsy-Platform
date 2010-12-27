<?php

namespace admin\models;

use lithium\storage\Session as LiSession;

class Promocode extends \admin\models\Base {
    
    protected $_meta = array('source' => 'promocodes');
    
	public static function createdBy($data){
		$user = LiSession::read('userLogin');
		$data['created_by']= $user['_id'];
		
		return $data;
	}
	    
}


?>
