<?php

namespace admin\controllers;

use admin\models\Credit;
use admin\models\User;
use admin\models\Event;
use admin\models\Order;
use lithium\util\Validator;

/**
 * The `Credits` Class provides functionality to give credits to users on an individual
 * basis or queries.
 *
 **/
class CreditsController extends \lithium\action\Controller {

	public function index() {
		$credits = Credit::all();
		return compact('credits');
	}

	public function view() {
		$credit = Credit::first($this->request->id);
		return compact('credit');
	}

	public function add() {
		$credit = Credit::create();
		$isMoney = Validator::isMoney($this->request->data['amount']);
		if (($isMoney) && ($this->request->data) && Credit::add($credit, $this->request->data)) {
			if (User::applyCredit($this->request->data)) {
				$this->redirect(array('Users::view', 'args' => array($this->request->data['user_id'])));
			}
		} else {
			$this->redirect(array('Users::view', 'args' => array(
				$this->request->data['user_id']
			)));
		}
		return compact('credit');
	}

	public function edit() {
		$credit = Credit::find($this->request->id);

		if (!$credit) {
			$this->redirect('Credits::index');
		}
		if (($this->request->data) && $credit->save($this->request->data)) {
			$this->redirect(array('Credits::view', 'args' => array($credit->id)));
		}
		return compact('credit');
	}
	/**
	 * Apply credit to an entire event.
	 * @param string
	 */
	public function eventCredit($eventId) {
		$event = Event::find('first', array(
			'conditions' => array(
				'_id' => $eventId
		)));
		if ($this->request->data) {
			if ($eventId) {
				$orders = Order::find('all', array(
					'conditions' => array(
						'items.event_id' => $eventId
				)));
				foreach ($orders as $order) {
					$credit = Credit::create();
					$data = array(
						'user_id' => (string) $order->user_id,
						'sign' => $this->request->data['sign'],
						'amount' => $this->request->data['amount']
					);
					User::applyCredit($data);
					$data = array(
						'reason' => $this->request->data['reason'],
						'sign' => $this->request->data['sign'],
						'amount' => $this->request->data['amount'],
						'description' => $this->request->data['description'],
						'user_id' => (string) $order->user_id,
						'event_id' => $eventId,
						'order_number' => (string) $order->order_id,
						'order_id' => (string) $order->_id
					);
					Credit::add($credit, $data);
				}
			}
		}

		$appliedCredit = Credit::find('all', array(
			'conditions' => array(
				'event_id' => $eventId
		)));

		return compact('appliedCredit', 'event');
	}

}

?>