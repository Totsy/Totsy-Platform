<?php

namespace admin\controllers;

use admin\models\Event;
use admin\models\Queue;
use admin\models\Order;
use admin\controllers\BaseController;
use lithium\storage\Session;
use MongoDate;
use MongoRegex;
use MongoCursor;
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
	 * @return compact $events - current events
	 * @return compact $queue - current events in queue
	 * @return compact $recent - recently processed
	 * @return compact $processedOrders - list of events that were processed for Orders
	 * @return compact $processedPOs - list of events that were processed for POs
	 */
	public function index() {
		$conditions = array(
			'end_date' => array(
				'$gte' => new MongoDate(strtotime("-5 week")),
				'$lte' => new MongoDate(time())
		    ),
		    'name' => array('$nin' => array(new MongoRegex("/(\btest\b|\btesting\b)/i"),new MongoRegex("/micah/i"))),
		    'tangible' => array('$ne' => false)
		);
		if ($this->request->data) {
			$search = $this->request->data['search'];
			$conditions = array('name' => new MongoRegex("/$search/i"));
		}
		$events = Event::find('all', compact('conditions'));
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
		//return compact('events', 'queue', 'recent', 'processedOrders', 'processedPOs');
		return compact('events', 'recent', 'processedOrders', 'processedPOs');
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
	/**
	* Retrieves the current information of queued events
	* @see admin/controllers/QueueController::index()
	**/
	public function currentQueue() {
	    MongoCursor::$timeout = 50000;
	    $this->_render['layout'] = false;
	    $conditions = array('processed' => array('$ne' => true));
		$queue = Queue::all(compact('conditions'));

		/**
		* Get the approx number of orders and lines files to be processed
		*/
		$size = 0;
		$json = array();
		$queue = $queue->data();

		foreach($queue as $data) {
		    $data['created_date'] = date('m-d-Y H:i:s', $data['created_date']);
		    $data['percent'] =  number_format($data['percent'], 1);
		    /**
		    * PO event count
		    **/
		    if (array_key_exists('purchase_orders', $data) && $data['purchase_orders']) {
		        if(!(array_key_exists('approx_info', $data)) || !(array_key_exists('purchase_orders', $data['approx_info']))) {
                    $data['purchase_orders'] = count($data['purchase_orders']);
                    Queue::update(array('$addToSet' => array('approx_info' => array(
                        'purchase_orders' => array(
                            'purchase_count' =>  $data['purchase_orders']
                    )))), array('_id' => $data['_id']));
		        } else {
		            $data['purchase_orders'] = $data['approx_info']['purchase_orders']['purchase_count'];
		        }
		    } else {
			    $data['purchase_orders'] = 0;
	            $data['order_count'] = 0;
	            $data['line_count'] = 0;
		    }

		    /**
		    * Order event count
		    **/
		    if(!(array_key_exists('approx_info', $data)) || !(array_key_exists('order', $data['approx_info']))) {
                   if (array_key_exists('orders', $data) && $data['orders']) {
                        $conditions = array(
                            'items.event_id' => array('$in' => $data['orders']),
                            'cancel' => array('$ne' => true)
                        );
                        $order_count = Order::count(compact('conditions'));

                        $fields = array('items' => true);
                        $orders = Order::find('all',compact('conditions','fields'));
                        $cancel_count = 0;
                        $line_count = 0;
                        /**
                        * Get the number of order lines
                        **/
                        foreach($orders as $order) {
                            $line_count += count($order['items']);
                            $items = $order['items'];
                            /**
                            * Get the number of canceled order lines
                            */
                            array_walk_recursive($items, function($item, $key, $cancel_count){
                                if ($key === 'cancel' && $item == true) {
                                    ++$cancel_count;
                                }
                            }, $cancel_count);
                        }
                        /**
                        * Get the actual number of order lines that will be processed
                        **/
                        $line_count -= $cancel_count;
                        $conditions = array(
                            'items.event_id' => array('$in' => $data['orders']),
                            'items.cancel' => true
                        );
                        $item_count = Order::count(compact('conditions'));
                        $data['orders'] = count($data['orders']);
                        $data['order_count'] = $order_count - $item_count;
                        $data['line_count'] = $line_count;
                        Queue::update(array('$addToSet' => array('approx_info' => array(
                            'order' => array(
                                'orders' =>  $data['orders'],
                                'line_count' =>  $data['line_count'],
                                'order_count' => $data['order_count']
                        )))), array('_id' => $data['_id']));
                    } else {
                    	$data['orders'] = 0;
                    	$data['order_count'] = 0;
	            		$data['line_count'] = 0;
                    }
		        } else {
		            $data['orders'] = $data['approx_info']['order']['orders'];
                    $data['order_count'] = $data['approx_info']['order']['order_count'];
                    $data['line_count'] = $data['approx_info']['order']['line_count'];
		        }
            unset($data['approx_info']);
            unset($data['summary']);
            $json[$size] = $data;
            ++$size;
		}
	    echo json_encode($json);
	}


}