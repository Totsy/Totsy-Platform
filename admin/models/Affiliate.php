<?php

namespace admin\models;

use MongoDate;


class Affiliate extends \admin\models\Base {

	protected $_meta = array('source' => 'users');

	protected $_schema=array(
			'invitation_codes'=>array('type'=>'array', 'null'=>false ),
			'affiliate'=>array('type'=>'boolean', 'null'=>false, 'default'=>true),
			'active'=>array('type'=>'boolean', 'null'=>false, 'default'=>true)
			);

	public $validates = array();
}

?>