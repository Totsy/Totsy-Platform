<?php

namespace admin\controllers;

use admin\models\Ticket;
use MongoDate;
use lithium\storage\Session;

class TicketsController extends BaseController {
	
	public function view() {
		
		$t_obj = new Ticket();
		$issue_list = $t_obj->getIssuesList();
		$tickets = null;
		$search_criteria = array();
		$skip = 0;
		if($this->request->data) {
			$data = $this->request->data;
			$condition = array();
			if(Session::check('search_criteria') && (array_key_exists('getNext', $data) || array_key_exists('goBack', $data) || array_key_exists('send_button', $data) || array_key_exists('sort', $data))){
				$search_criteria = Session::read('search_criteria');
				$data = $data + $search_criteria;

				if ($search_criteria['search_by'] == 'date') {
					$data['start_date'] = $search_criteria['search_by_value']['start_date'];
					$data['end_date'] = $search_criteria['search_by_value']['end_date'];
				} else {
					$data[$search_criteria['search_by']] = $search_criteria['search_by_value'];
				}
			} else {
				Session::delete('search_criteria');
			}
			
			if(array_key_exists('getNext', $data)) {
				$skip = ($data['getNext'] + 1) * $search_criteria['limit_by'];
				$getNext = $data['getNext'] + 1;
			} else if (array_key_exists('goBack', $data)) {
				$skip = ($data['goBack'] - 1) * $search_criteria['limit_by'];
				if ($data['goBack'] > 1) {
					$getNext = $data['goBack'] - 1;
				}
			}else {
				$getNext = 1;
			}
			if(array_key_exists('sort_by', $data)) {
				$sort_by = $data['sort_by'];
				$order_by = (int)$data['order_by'];
			}else {
				$sort_by = "date_created";
				$order_by = -1;
			}
			$search_criteria['limit_by'] = $data['limit_by'];

			if(array_key_exists('send_button', $data)) {
				Ticket::sendToLiveperson($data);
			}
			extract(Ticket::getConditions($data, $search_criteria),EXTR_OVERWRITE);

			Session::write('search_criteria', $search_criteria);
			$ticketsCol = Ticket::collection();
			$tickets = $ticketsCol->find($condition)->limit($data['limit_by'])->skip($skip)->sort(array($sort_by => $order_by));

		}
		return compact('issue_list', 'tickets', 'search_criteria', 'getNext','sort_by','order_by');
	}
}

?>