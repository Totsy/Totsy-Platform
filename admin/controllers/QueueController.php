<?php

namespace admin\controllers;

use admin\models\Event;
use admin\models\Queue;
use admin\controllers\BaseController;
use lithium\storage\Session;
use MongoDate;
use MongoRegex;
use MongoId;
use li3_flash_message\extensions\storage\FlashMessage;
use admin\extensions\util\String;

/**
 * The Queue Controller
 *
 **/
class QueueController extends BaseController {

	/**
	 * Shows all the events that have ended in the past two weeks.
	 * This method provides the first step in select all the events
	 * that should be added to the queue for processing.
	 * @see admin\models\Event
	 * @return compact $events
	 */
	public function index() {
		$conditions = array(
			'end_date' => array(
				'$gte' => new MongoDate(strtotime("-2 week")),
				'$lte' => new MongoDate(time())
		));
		if ($this->request->data) {
			$search = $this->request->data['search'];
			$conditions = array('name' => new MongoRegex("/$search/i"));
		}
		$events = Event::find('all', compact('conditions'));
		$queue = Queue::all();
		return compact('events', 'queue');
	}

	/**
	 * Adds an event id to the queue and flags it for order and/or PO processing.
	 * 
	 * The view will contain a checkboxes that will directly correspond to the
	 * document that should be saved to the queue collection.
	 *
	 * @see admin\models\Queue
	 * @return object
	 */
	public function add() {
		if ($this->request->data) {
			$data = $this->request->data;
			$queue = Queue::create();
			$queue->orders = (!empty($data['orders'])) ? $data['orders']: null;
			$queue->purchase_orders = (!empty($data['pos'])) ? $data['pos']: null;
			if ($queue->orders || $queue->purchase_orders) {
				$queue->created_date = new MongoDate();
				$queue->save();
			}
		}
		$this->redirect('Queue::index');
	}

	

	
}