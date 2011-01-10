<?php

namespace admin\models;

class BackOrdered extends \admin\models\Base {

	public $validates = array();

	protected $_meta = array('source' => 'orders.backordered');
}

?>