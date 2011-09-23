<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\Order;
use admin\models\Item;
use MongoDate;
use MongoRegex;
use MongoId;

/**
 * Fix orders with free shipping service
 */
class FreeShippingServiceFix extends \lithium\console\Command {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';
	public $beginning = "Today";
	public $end = "Now";

	public function run() {
		$this->header('Launching Free Shipping Service Fix....');
		Environment::set($this->env);
		$this->_fix();
		$this->out('Finished Free Shipping Service Fix');
	}

	/**
	 * The _fix method melts your brain
	 *
	 */
	protected function _fix() {
		// orders { oversizehandling, handling, service, promo_discount, promo_code, subTotal, tax, total }
		// items { shipping_exempt, shipping_oversize, shipping_rate }
		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate  = new MongoDate(strtotime($this->end));
		$conditions = array(
			'service' => 'freeshipping',
			'handling_discount' => array('$exists' => false),
			'overSizeHandling_discount' => array('$exists' => false),
			'date_created' => array(
				'$gte' => $startDate,
				'$lt' => $endDate)
		);
		
		// Find all orders with free shipping service
		$orders = Order::find('all', array('conditions' => $conditions));//, 'limit' => 10
		
		// For each item in an order
		// If shipping_exempt on ALL items, handling_discount = 0
		// If shipping_oversize add to overSizeHandling total
		// If neither of the above set handling_discount to 7.95
		
		$i = 0;
		foreach ($orders as $order) {
			
			$handling_discount = 0;
			$overSizeHandling_discount = 0;
			
			$order = $order->data();
			foreach($order['items'] as $orderItem) {
				// Retrieve the original item record
				$item = Item::find('first', array('conditions' => array('_id' => new MongoId($orderItem['item_id']))));
				if ($item!=null)
					$item = $item->data();
				
				if (isset($item['shipping_exempt']) && $item['shipping_exempt'] == true) {
					continue;
				} else if (isset($item['shipping_oversize']) && $item['shipping_oversize'] == "1") {
					$overSizeHandling_discount += $item['shipping_rate'];
				} else {
					$handling_discount = 7.95;
				}
			}
			
			if ($handling_discount > 0 || $overSizeHandling_discount > 0) {
				$i++;
				// Add handling_discount and overSizeHandling_discount fields to order record
				$order = Order::find($order['_id']);
				$order->handling_discount = $handling_discount;
				$order->overSizeHandling_discount = $overSizeHandling_discount;
				$order->save();
				
				/*$this->out('Order: ' . $order['_id']);
				$this->out('handling_discount: ' . $handling_discount);
				$this->out('overSizeHandling_discount: ' . $overSizeHandling_discount);
				$this->out('');
				exit;*/
			}
		}

		$this->out('Fixed ' . $i . ' order records.');
	}
}