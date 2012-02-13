<?php

namespace admin\controllers;

use admin\models\Ticket;
use MongoDate;

class TicketsController extends BaseController {
	
	public function view() {
		
		$t_obj = new Ticket();
		$issue_list = $t_obj->getIssuesList();
		$tickets = null;
		$search_criteria = array();
		if($this->request->data) {
			$data = $this->request->data;
			
			$condition = array();

			if($data['issue_type'] != "any") {
				$condition['issue.issue_type'] = $data['issue_type'];
				$search_criteria['issue_type'] = $data['issue_type'];
			}

			switch($data['search_by']) {
				case 'email':
					$condition['user.email'] = $data['email'];
					$search_criteria['search_by'] = 'email';
					$search_criteria['search_by_value'] = $data['email'];
					break;
				case 'month':
					$condition['$where'] = "this.date_created.getMonth()==" . $data['month'];
					$search_criteria['search_by'] = 'month';
					$search_criteria['search_by_value'] = $data['month'];
					break;
				case 'date':
				//	$condition['date_created'] = array('$gte' => new MongoDate(strtotime($data['']));
					$search_criteria['search_by'] = 'date';
					$search_criteria['search_by_value'] = $data['date'];
					break;
			};
			$ticketsCol = Ticket::collection();
			$tickets = $ticketsCol->find($condition)->limit($data['limit_by']);

		}

		return compact('issue_list', 'tickets', 'search_criteria');
	}
}

?>