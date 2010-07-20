<?php

namespace app\controllers;

use app\models\Ticket;
use app\models\Transaction;
use app\controllers\BaseController;
use \lithium\storage\Session;

class TicketsController extends BaseController {
	
	public function view() {
		$user = Session::read('userLogin');
		$ticket = Ticket::find('all', array(
			'conditions' => array('username' => $user['username'])
		));
		return compact('ticket');
	}

	public function add() {
		$ticket = Ticket::create();
		$user = Session::read('userLogin');
		$transactions = Transaction::find('all', array(
			'conditions' => array(
				'username' => $user['username']
		)));
		if (!empty($transactions)) {
			$first = "---Please select an order---";
			$orders = array($first);
		} else {
			$order = array();
		}
		unset($user['password']);
		unset($user['_id']);
		$data = array_merge($this->request->data, $user);
		if (($this->request->data) && $ticket->save($data)) {
			$message = "Your ticket has been submitted to Totsy. Thank You!";
		}
		return compact('ticket', 'message', 'orders');
	}

}

?>