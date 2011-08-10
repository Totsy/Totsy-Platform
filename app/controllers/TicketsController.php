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
			$options = array();
			
			if (is_object($user)){
				if (isset($user->email) && !empty($user->email)){ 
					$options['replyto'] = $options['behalf_email'] = $user->email;
				} else if (isset($user->confirmemail) && !empty($user->confirmemail)){
					$options['replyto'] = $options['behalf_email'] = $user->confirmemail;
				}
			} else if (is_array($user)){
				if (array_key_exists('email',$user) && !empty($user['email'])){
					$options['replyto'] = $options['behalf_email'] = $user['email'];
				} else if (array_key_exists('confirmemail',$user) && !empty($user['confirmemail'])){
					$options['replyto'] = $options['behalf_email'] = $user['confirmemail'];
				} 
			}
			Mailer::send('Tickets', $email, $args, $options);
			$this->_render['template'] = 'sent';
		}

		return compact('ticket', 'message', 'orders');
	}

}

?>