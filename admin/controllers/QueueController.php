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
 * @todo Show all the POs that have already been processed
 * @todo Open PO tab, Sent PO Tabs
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
				'$gte' => new MongoDate(strtotime("-5 week")),
				'$lte' => new MongoDate(time())
		));
		if ($this->request->data) {
			$search = $this->request->data['search'];
			$conditions = array('name' => new MongoRegex("/$search/i"));
		}
		$events = Event::find('all', compact('conditions'));
		$conditions = array('processed' => array('$ne' => true));
		$queue = Queue::all(compact('conditions'));
		$conditions = array('processed' => true);
		$recent = Queue::all(compact('conditions'));
		/**
		 * Show all the data
		 */
		$data = $recent->data();
		$processedOrders =  $processedPOs = array();
		foreach ($data as $records) {
			if ($records['orders']) {
				$processedOrders = array_unique(array_merge($processedOrders, $records['orders']));
			}
			if ($records['purchase_orders']) {
				$processedPOs = array_unique(array_merge($processedPOs, $records['purchase_orders']));
			}
		}
		return compact('events', 'queue', 'recent', 'processedOrders', 'processedPOs');
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

	/**
	 * Allows the admin to view the details of a Queue job.
	 * @param string $id The object id of the queue
	 * @return compact
	 */
	public function view($id) {
		if ($id) {
			$queue = Queue::find($id);
			if ($queue) {
				$orders = $queue->orders->data();
				$conditions = array('_id' => $queue->orders->data());
				$orderEvents = Event::all(compact('conditions'));
			}
		}
		return compact('orderEvents');
	}
	

	
}