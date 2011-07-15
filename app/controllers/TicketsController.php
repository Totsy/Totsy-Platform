<?php

namespace app\controllers;

use app\models\Ticket;
use app\models\Order;
use app\controllers\BaseController;
use \lithium\storage\Session;
use app\extensions\Mailer;

class TicketsController extends BaseController {
	
	public function view() {
		$user = Session::read('userLogin');
		$ticket = Ticket::find('all', array(
			'conditions' => array('email' => $user['email'])
		));
		return compact('ticket');
	}

	/**
	 * Add a ticket to the database and send email to @totsy rep.
	 */
	public function add() {
		$ticket = Ticket::create();
		$user = Session::read('userLogin');
		$orders = Order::findAllByUserId((string) $user['_id'])->invoke('summary', array(), array(
			'merge' => true
		));

		$list = Ticket::$issueList;
		$agent = array('user_agent' => $this->request->env('HTTP_USER_AGENT'));
		$args = array('issue' => $this->request->data) + array('user' => $user) + $agent;
		if (($this->request->data) && $ticket->save($args)) {
			$email = $list[$args['issue']['issue_type']]; 
			Mailer::send('Tickets', $email, $args);
			//$this->redirect('tickets/sent');
			$this->_render['template'] = 'sent';
		}

		return compact('ticket', 'message', 'orders');
	}

}

?>