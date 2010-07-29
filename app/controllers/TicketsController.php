<?php

namespace app\controllers;

use app\models\Ticket;
use app\models\Order;
use app\controllers\BaseController;
use \lithium\storage\Session;

class TicketsController extends BaseController {
	
	public function view() {
		$user = Session::read('userLogin');
		$ticket = Ticket::find('all', array(
			'conditions' => array('email' => $user['email'])
		));
		return compact('ticket');
	}

	public function add() {
		$ticket = Ticket::create();
		$user = Session::read('userLogin');
		$orders = Order::findAllByUserId((string) $user['_id'])->invoke('summary', array(), array(
			'merge' => true
		));

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