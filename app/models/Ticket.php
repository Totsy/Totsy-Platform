<?php

namespace app\models;

class Ticket extends \lithium\data\Model {

	public $validates = array();

	public static $issueList = array(
		'order' => 'support@totsy.com',
		'tech'=> 'support@totsy.com',
		'cs' => 'support@totsy.com',
		'business' => 'business@totsy.com',
		'press' => 'press@totsy.com'
	);

}

?>