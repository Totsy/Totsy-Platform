<?php

namespace li3_silverpop\models;
use MongoDate;

class Log extends \lithium\data\Model {

	public $validates = array();

	protected $_meta = array('source' => 'silverpop.log');

}

?>