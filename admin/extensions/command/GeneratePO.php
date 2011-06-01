<?php

namespace admin\extensions\command;

use admin\models\Item;
use admin\models\Order;
use admin\models\Event;
use admin\models\PurchaseOrder;
use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use MongoCursor;
use admin\extensions\command\Base;
use lithium\analysis\Logger;
use admin\extensions\util\String;
use admin\extensions\command\Pid;

/**
 * This command is for processing POs for vendors to be viewed
 *
 * The export specification is based on the DotCom flat file integration.
 * Based on what has been queued from the admin system all the event ids will be processed
 * for order, item and/or PO transmission.
 *
 * @see admin/controllers/ReportController::purchases
 */
class GeneratePO extends Base {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	 * PO Events for processing
	 * @var array
	 */
	protected $poEvents = array();

	/**
	 * Allows verbose info logging. (default = false)
	 */
	public $verbose = 'false';

	/**
	* Process initial set of purchase_orders
	*
	*/
	public $initial = 'false';

	/**
	* Set starting date of the end_date to search by.  Format is mm/dd/YYYY.
	* Used in combination to initial param
	**/
	public $startrng = "";

	/**
	* Set ending date of the end_date to search by. Format is mm/dd/YYYY
	* Used in combination to initial param
	**/
	public $endrng = "";

	/**
	*
	*
	**/
	public $event = "";

	/**
	 * Main method for generating POs when in the background
	 *
	 * The `run` method will query the pending event transactions
	 * that have not yet been processed. This queuing system will be managed
	 * from the admin dashboard.
	 *
	 * @todo Remove the environment set to the base command.
	 */
	public function run() {
	    Environment::set($this->env);
	    $start = time();
	    $this->_expiredEvents();
	    $this->_purchases();
	    $end = time();
	    $finish = $end - $start;
	    $this->out("It took ". $finish . "secs to finish");
	}
	/**
	 * The purchases method generates the PO report for the logistics team. This report returns an associative array
	 * which lists all the sales of each item of a sale.
	 *
	 * The order of operation is as follows:
	 *
	 * 1) Find all the event that is being requested in the eventList array.
	 * 2) Find all the items that are a part of the event requested.
	 * 3) For each item get all the orders that have been placed with that item in it.
	 * 4) Build the array of cumulative purchases for each item of the event.
	 * @return mixed
	 */
	protected function _purchases() {
	    MongoCursor::$timeout = -1;
		$this->log('Generating Purchase Orders');
		$orderCollection = Order::collection();
		foreach ($this->poEvents as $eventId) {
		    $eventId = (string) $eventId->_id;
			//$purchaseHeading = ProcessedOrder::$_purchaseHeading;
			$total = array('sum' => 0, 'quantity' => 0);
			$event = Event::find('first', array(
				'conditions' => array(
					'_id' => $eventId),
				'fields' => array(
				    '_id' => 1,
				    'name' => 1
				)));
			$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr(String::asciiClean($event->name), 0, 3));
			$time = date('ymdis', $event->_id->getTimestamp());
			$poNumber = 'TOT' . '-' . $vendorName . $time;
			$eventItems = $this->_getOrderItems($eventId);
			$purchaseOrder = array();
			$po = PurchaseOrder::collections("vendorpo");
			$po->remove(array("eventId" => $eventId));
			$inc = 0;
			foreach ($eventItems as $eventItem) {
				foreach ($eventItem['details'] as $key => $value) {
					$orders = Order::find('all', array(
						'conditions' => array(
							'items.item_id' => (string) $eventItem['_id'],
							'items.size' => (string) $key,
							'cancel' => array('$ne' => true)),
						'fields' => array('items' => 1)
						));
					$count = count($orders);
					$this->log("There are $count that has item $eventItem[_id]");
					if ($orders) {
						$orderData = $orders->data();
						if (!empty($orderData)) {
							foreach ($orderData as $order) {
								$items = $order['items'];
								foreach ($items as $item) {
									$active = (empty($item['cancel']) || !$item['cancel']) ? true : false;
									$itemValid = ($item['item_id'] == $eventItem['_id']) ? true : false;
									if ($itemValid && ((string) $key == $item['size']) && $active){
										$purchaseOrder[$inc]['Product Name'] = $eventItem['description'];
										$purchaseOrder[$inc]['Product Color'] = $eventItem['color'];
										$purchaseOrder[$inc]['Vendor Style'] = $eventItem['vendor_style'];
										$itemRecord = Item::collection()->findOne(array('_id' => new MongoId($item['item_id'])));
										$purchaseOrder[$inc]['SKU'] = $itemRecord['sku_details'][$item['size']];
										$purchaseOrder[$inc]['Unit'] = $eventItem['sale_whol'];
										if (empty($purchaseOrder[$inc]['Quantity'])) {
											$purchaseOrder[$inc]['Quantity'] = $item['quantity'];
										} else {
											$purchaseOrder[$inc]['Quantity'] += $item['quantity'];
										}
										$purchaseOrder[$inc]['Total'] = $purchaseOrder[$inc]['Quantity'] * $eventItem['sale_whol'];
										$purchaseOrder[$inc]['Size'] = $item['size'];
										$purchaseOrder[$inc]["PO"] = $poNumber;
										$purchaseOrder[$inc]["eventId"] = $eventId;
									}
								}
							}
							if (!empty($purchaseOrder[$inc])) {
								$po->save($purchaseOrder[$inc]);
							}
							++$inc;
						}
					}
				}
			}
		}
	}
	/**
	 * Return all the items of an event.
	 */
	protected function _getOrderItems($eventId = null) {
		$items = null;
		if ($eventId) {
			$items = Item::find('all', array(
				'conditions' => array(
					'event' => array('$in' => array($eventId)
			    )),
			    'fields' => array(
			        '_id' => 1,
			        'color' => 1,
			        'description' => 1,
			        'vendor_style' => 1,
			        'sale_whol' => 1,
			        'details' => 1)
			));
			$count = count($items);
			$this->log('Event id $eventid has $items items.');
			$items = $items->data();
		}
		return $items;
	}
	/**
	* The expired events method retrieves all events that ended today between 10am - 11pm
	*/
	protected function _expiredEvents() {
	    $condition = array();
        if (!empty($this->event)){
	        $condition = array_merge($condition, array('_id' => $this->event));
	    }
	    if ($this->initial == "true") {
	        if(empty($this->startrng)) {
	            $this->out(var_dump($this->startrng));
	            $this->startrng = date("m/d/Y");
	        }
	        if(empty($this->endrng)) {
	            $this->out(var_dump($this->endrng));
	            $this->endrng = date("m/d/Y");
	        }
	        $condition = array("end_date" => array(
            '$gte' => new MongoDate(strtotime($this->startrng . "10:00:00")),
            '$lte' => new MongoDate(strtotime($this->endrng . "10:59:59"))
           ));
	    } else {
           $condition = array("end_date" => array(
            '$gte' => new MongoDate(strtotime(date("m/d/Y") . "10:00:00")),
            '$lte' => new MongoDate(strtotime(date("m/d/Y") . "10:59:59"))
           ));
	   }
	   	$expired = Event::find('all', array("conditions" => $condition, "fields" => array('_id' => 1)));
	   	$this->poEvents = $expired;
	    $amount = count($this->poEvents);
	    $this->log('$amount event(s) have closed today.');
	    $this->out("$amount event(s) are closed today.");
	}



}