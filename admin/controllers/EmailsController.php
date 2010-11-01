<?php

namespace admin\controllers;

use \admin\models\Email;
use \admin\models\Event;
use \admin\models\User;
use \admin\models\Order;
use \admin\extensions\Mailer;
use MongoDate;
use MongoId;
use li3_flash_message\extensions\storage\FlashMessage;
use li3_silverpop\extensions\Silverpop;

/**
 * undocumented class
 *
 **/

class EmailsController extends \lithium\action\Controller {

	public function index() {
		$events = Event::all(array('order' => array('end_date' => 'ASC')));
		return compact('events');
	}

	public function view() {
		$email = Email::first($this->request->id);
		return compact('email');
	}

	public function select($eventId = null) {
		
		$email = Email::create();

		if ($eventId) {
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
		}
		if ($this->request->data) {
			$order = Order::find('first', array('conditions' => array('_id' => '4c991893ce64e5c10fce0500')));
			$orders = Order::find('all', array(
				'conditions' => array(
					'items.event_id' => $eventId
			)));
			if ($orders) {
				foreach ($orders as $order) {
					$user = User::find('first', array(
						'conditions' => array(
							'_id' => $order->user_id
					)));
					$data = array(
						'email' => 'esmith@totsy.com',
						'order' => $order,
						'event' => $event,
						'note' => $this->request->data['note'],
						'campaignId' => $this->request->data['campaignId']
					);
					if (Silverpop::send('orderStatus', $data)) {
						$log = array('body' => $data) + array(
							'event_id' => $eventId,
							'template' => 'orderStatus',
							'created_date' => Email::dates('now')
						);
						$email->save($log);
					}
				}
			}
		}
		return compact('emailLog', 'event', 'emailTypes');
	}
}

?>