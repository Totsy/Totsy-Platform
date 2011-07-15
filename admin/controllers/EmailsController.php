<?php

namespace admin\controllers;

use admin\models\Email;
use admin\models\Event;
use admin\models\User;
use admin\models\Order;
use admin\extensions\Mailer;
use MongoDate;
use MongoId;
use li3_flash_message\extensions\storage\FlashMessage;
use lithium\storage\Session;

/**
 * The `Emails` class is the gateway to send manual order status update
 * emails to all the customers associated with an event.
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

	/**
	 * The main method to handle the manual email transactional emails.
	 * @param string $eventId
	 * @return array
	 * @todo Redirect admin to an event status page.
	 */
	public function send($eventId = null) {
		if ($eventId) {
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId
			)));
		}
		if ($this->request->data) {
			$orders = Order::find('all', array(
				'conditions' => array(
					'items.event_id' => $eventId
			), 'limit' => 1));
			if ($orders) {
				foreach ($orders as $order) {
					$user = User::find('first', array(
						'conditions' => array(
							'_id' => $order->user_id
					)));
					$this->_send($user, $order, $event->name, $this->request->data);
					$status = '3';
					$this->_update($order, $status);
				}
			}
		}
		return compact('event');
	}

	/**
	 * Updates the order status based on what email was sent to the customer.
	 *
	 * @param object $order
	 * @param string $status
	 * @return boolean
	 */
	protected function _update($order, $status) {

	}

	/**
	 * This method interacts with the li3_silverpop library to process the XML
	 * transaction.
	 *
	 * @param object $user
	 * @param object $order
	 * @param object $event
	 * @param array $post
	 * @return boolean
	 */
	protected function _send($user, $order, $event, $post) {
		$admin = Session::read('userLogin');
		$email = Email::create();
		$data = array(
			'email' => 'fagard@totsy.com',
			'order' => $order,
			'event' => $event,
			'note' => $post['note']
		);
		$log = array(
			'created_date' => Email::dates('now'),
			'admin_id' => $admin['_id'],
			'admin_name' => $admin['firstname']
			) + $data;
		$email->save($log);
		$data['SPOP'] = $email->_id;
		$email->success = (Mailer::send($post['template'], $data['email'], $data)) ? true : false;
		return $email->save();
	}
}

?>