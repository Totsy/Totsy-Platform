<?php

namespace app\controllers;

use app\models\Ticket;
use app\models\Order;
use app\controllers\BaseController;
use \lithium\storage\Session;
use li3_silverpop\extensions\Silverpop;

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
		//$email = array('email' => $list[$data['issue']['issue_type']]);
		$email = array('email' => 'fhagard@lightcube.us');
		$data = array('issue' => $this->request->data) + array('user' => $user) + $agent + $email;
		if (($this->request->data) && $ticket->save($data)) {
			Silverpop::send('general', $data);
			$this->redirect('ticket/sent');
		}

		return compact('ticket', 'message', 'orders');
	}

}

?>