<?php

namespace app\controllers;

use app\models\Ticket;
use app\models\Menu;
use app\controllers\BaseController;
use \lithium\storage\Session;

class TicketsController extends BaseController {
	
	protected function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$menu = Menu::find('all', array(
				'conditions' => array(
					'location' => 'left', 
					'active' => 'true'
			)));
			$self->set(compact('menu'));
			return $chain->next($self, $params, $chain);
		});
	}
	
	public function view() {
		$ticket = Ticket::first($this->request->id);
		return compact('ticket');
	}

	public function add() {
		$ticket = Ticket::create();
		
		$user = Session::read('userLogin');
		unset($user['password']);
		unset($user['_id']);
		$data = array_merge($this->request->data, $user);
		if (($this->request->data) && $ticket->save($data)) {
			$message = "Your ticket has been submitted to Totsy. Thank You!";
		}
		return compact('ticket', 'message');
	}

}

?>