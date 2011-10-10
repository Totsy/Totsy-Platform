<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\Order;
use admin\models\Item;
use admin\models\Promocode;
use MongoDate;
use MongoRegex;
use MongoId;

/**
 * Fix orders with that use promo codes greater than their subtotal
 */
class PromoDiscountFix extends \lithium\console\Command {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';
	public $beginning = "Today";
	public $end = "Now";

	public function run() {
		$this->header('Launching Promo Discount Fix....');
		Environment::set($this->env);
		$this->_fix();
		$this->out('Finished Promo Discount Fix');
	}

	/**
	 * The _fix method melts your brain
	 *
	 */
	protected function _fix() {
		// orders { oversizehandling, handling, service, promo_discount, promo_code, subTotal, tax, total }
		
		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate  = new MongoDate(strtotime($this->end));
		// Find all orders with free shipping service
		$conditions = array(
			'promo_discount' => array('$exists' => true),
			'promo_code' => array('$exists' => true),
			'promo_actual' => array('$exists' => false),
			'date_created' => array(
				'$gte' => $startDate,
				'$lt' => $endDate)
		);
		$orders = Order::find('all', array('conditions' => $conditions));//, 'limit' => 10
		
		// For each order calculate the amount of the promo_discount that was really used
		// If it was less than the value then save in promo_actual
		$i = 0;
		foreach ($orders as $order) {
			$promo_actual = 0;
			
			$order = $order->data();
			if ($order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00"))
				continue;
			// Fix orders that have 10off50 discount set but it wasn't actually applied
/*			if (isset($order['discount'])) {
				if ($order['subTotal'] < 50 && $order['discount'] == -10)
					$order['discount'] = 0;
			}*/
			
			$x = $order['subTotal'] + $order['promo_discount'];
			if (isset($order['discount']))
				$x += $order['discount'];
			if (isset($order['credit_used']))
				$x += $order['credit_used'];
			
			if (number_format($x,2, '.', '') < 0) {
				$i++;
				
				$promo_actual = $order['promo_discount'] - $x;
				
				// Add promo_actual to order record
				$order = Order::find($order['_id']);
				$order->promo_actual = $promo_actual;
				$order->save();
				
				/*$this->out('Order: ' . $order['_id']);
				$this->out('promo_actual: ' . $order->promo_actual);
				$this->out('subTotal: ' . $order->subTotal);
				$this->out('promo_discount: ' . $order->promo_discount);
				if (isset($order['discount']))
					$this->out('discount: ' . $order['discount']);
				if (isset($order['credit_used']))
					$this->out('credit_used: ' . $order['credit_used']);
				$this->out('');
				exit;*/
			}
		}

		$this->out('Fixed ' . $i . ' order records.');
	}
}