<?php

namespace app\controllers;

use app\models\Ticket;
use app\models\Order;
use app\controllers\BaseController;
use \lithium\storage\Session;
use app\extensions\Mailer;
use app\models\User;

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
	 * 
	 * TODO: in fufture make normal form errors error reports no extra params (data and error)
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
		$data = null;
		$error = null;
		if ($this->request->data){
			$data = $this->request->data;
			$error = User::validateContactUs($data);
		}
		
		if (!is_null($data) && (is_null($error) || $error==true)) {
		
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
		}

		return compact('ticket', 'message', 'orders', 'user','error','data');
	}

}

?>