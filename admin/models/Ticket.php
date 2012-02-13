<?php

namespace admin\models;

class Ticket extends Base {
	
	protected $_meta = array('source' => 'tickets');

	protected $issue_types = array(
		'any' => 'any',
		'default' => 'default',
		'order' => 'order',
		'tech'=> 'tech',
		'shipping'=> 'shipping',
		'refunds' => 'refunds',
		'merch' => 'merch',
		'business' => 'business',
		'press' => 'press'
	);

	public function getIssuesList() {
		return $this->issue_types;
	}

	public function sendToLiveperson(){
	}

}

?>