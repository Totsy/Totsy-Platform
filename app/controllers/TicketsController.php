<?php

namespace app\controllers;

use app\models\Ticket;
use app\models\Order;
use app\controllers\BaseController;
use lithium\storage\Session;
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
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_add';
		} else {
			// we're doing this to disable the contact form.
			$this->redirect("/pages/contact");
		}
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
			
		if (!is_null($data) && !is_array($error)) {

			if (($this->request->data) && $ticket->save($args)) {
				$email = $list[$args['issue']['issue_type']];
				$options = array();
				$name = $data['firstname'].' '.$data['lastname'];
				if (is_object($user)){
					$args['user']->firstname = $data['firstname'];
					$args['user']->lastname = $data['lastname'];
					$args['user']->telephone = $data['telephone'];
					if (isset($user->email) && !empty($user->email)){ 
						$options['replyto'] = $options['behalf_email'] = $user->email;
					} else if (isset($user->confirmemail) && !empty($user->confirmemail)){
						$options['replyto'] = $options['behalf_email'] = $user->confirmemail;
					}
				} else if (is_array($user)){
					$args['user']['firstname'] = $data['firstname'];
					$args['user']['lastname'] = $data['lastname'];
					$args['user']['telephone'] = $data['telephone'];
					if (array_key_exists('email',$user) && !empty($user['email'])){
						$options['replyto'] = $options['behalf_email'] = $user['email'];
					} else if (array_key_exists('confirmemail',$user) && !empty($user['confirmemail'])){
						$options['replyto'] = $options['behalf_email'] = $user['confirmemail'];
					} 
				}
				Mailer::send('Tickets', $email, $args, $options);
				if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia'){
				 	$this->_render['layout'] = 'mobile_main';
				 	$this->_render['template'] = 'mobile_sent';
				} else {
					$this->_render['template'] = 'sent';
				}
			}
		}

		return compact('ticket', 'message', 'orders', 'user','error','data');
	}

}

?>