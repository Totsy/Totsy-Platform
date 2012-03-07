<?php

namespace admin\models;

use lithium\core\Environment;
use admin\extensions\Mailer;
use MongoDate;
use MongoId;
use MongoRegex;

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

	public static function sendToLiveperson($request) {
		$condition = array();
		if (array_key_exists('send_button', $request)) {
			switch($request['send_button']) {
				case 'all':
					extract(static::getConditions($request), EXTR_OVERWRITE);
					$ticketsCol = static::collection();
					$tickets = $ticketsCol->find($condition);
					break;
				case 'selected':
					$request['send'] = array_unique($request['send']);
					$ids = array();
					foreach($request['send'] as $id){
						if(!empty($id)){
							$ids[] = new MongoId($id);
						}
					}
					$condition = array('_id' => array('$in' => $ids));
					$ticketsCol = static::collection();
					$tickets = $ticketsCol->find($condition);
					break;
			}
		}
		foreach($tickets as $ticket) {
		//	$email = $ticket['user']['email'];
		//	$email = 'lhanson@totsy.com';
			if (array_key_exists('email', $ticket['user']) && !empty( $ticket['user'])){
				$options['replyto'] = $options['behalf_email'] = $email;
			} else if (array_key_exists('confirmemail',$ticket['user']) && !empty($ticket['user']['confirmemail'])){
				$options['replyto'] = $options['behalf_email'] = $ticket['user']['confirmemail'];
			} 
			$ticket['date_created'] = date('m/d/Y H:i:s', $ticket['date_created']->sec);
			//$status = Mailer::send('Tickets', $email, $ticket, $options);
		//	if (array_key_exists('error', $status)) {
				static::collection()->update(array('_id' => $ticket['_id']),array(
						'$set' => array('status' => 'Pending')
					));
			/*} else {
				static::collection()->update(array('_id' => $ticket['_id']),array(
						'$set' => array('status' => 'Sent' , 'date_sent' => new MongoDate())
					));
			}*/
		}
		$env = 'production';
		if (!Environment::is('production')) {
			$env = 'development';
		}
		exec("(cd /var/www/current/admin; /usr/local/lithium/console/li3 send-pending-tickets --env={$env}) &> /dev/null &");
	}

	public static function getConditions($request, $search_criteria = array()) {
		$condition = array('$where' => "this.date_created.getFullYear() > '2010'");
		if(($request['issue_type'] != "any") && $request['issue_type']) {
			$condition['issue.issue_type'] = $request['issue_type'];
			$search_criteria['issue_type'] = $request['issue_type'];
		} else {
			unset($request['issue_type']);
		}

		switch($request['search_by']) {
			case 'email':
				$condition['user.email'] = $request['email'];
				$search_criteria['search_by'] = $request['search_by'];
				$search_criteria['search_by_value'] = $request['email'];
				break;
			case 'month':
				$condition['$where'] = "this.date_created.getMonth()==" . $request['month'];
				$search_criteria['search_by'] = $request['search_by'];
				$search_criteria['search_by_value'] = $request['month'];
				break;
			case 'date':
				$condition['date_created'] = array(
						'$gte' => new MongoDate(strtotime($request['start_date'])),
						'$lte' => new MongoDate(strtotime($request['end_date']))
					); 
				$search_criteria['search_by'] = $request['search_by'];
				$search_criteria['search_by_value'] = array(
					'start_date' => $request['start_date'],
					'end_date' => $request['end_date']
				);
				break;
			case 'keyword':
				$condition['$or'] = array(
						array('user.email' => new MongoRegex('/' . $request['keyword'] .'/i')),
						array('issue.type' => new MongoRegex('/' . $request['keyword'] .'/i')),
						array('issue.message' => new MongoRegex('/' . $request['keyword'] .'/i'))
					);
				$search_criteria['search_by'] = $request['search_by'];
				$search_criteria['search_by_value'] = $request['keyword'];
				break;
		};
		return compact('condition','search_criteria');	
	}

}

?>