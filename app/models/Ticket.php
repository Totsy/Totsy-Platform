<?php

namespace app\models;

class Ticket extends \lithium\data\Model {

	public $validates = array();

	public static $issueList = array(
		'default' => 'support@totsy.com',
		'order' => 'support@totsy.com',
		'tech'=> 'support@totsy.com',
		'shipping'=> 'support@totsy.com',
		'refunds' => 'support@totsy.com',
		'merch' => 'support@totsy.com',
		'business' => 'business@totsy.com',
		'press' => 'press@totsy.com'
	);

}

?>