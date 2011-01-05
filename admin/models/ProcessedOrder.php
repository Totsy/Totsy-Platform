<?php

namespace admin\models;

class ProcessedOrder extends \lithium\data\Model {

	public $validates = array();

	protected $_meta = array('source' => 'orders.processed');
}

?>