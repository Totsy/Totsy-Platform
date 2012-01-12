<?php

namespace admin\models;
use admin\models\Base;

class Invitation extends Base {

	protected $_meta = array('source' => 'invitations');
	
	public $validates = array();
}

?>