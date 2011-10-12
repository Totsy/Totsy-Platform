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
 * The value in orders.promo_discount is incorrect when the promo code used is greater than the order subtotal.
 * So if a $30 plum direct code were used for a $25 order promo_discount is stored as $30 but should be $25
 * This script will check all orders and set promo_discount to the correct value as needed.
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

	protected function _fix() {
		
		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate  = new MongoDate(strtotime($this->end));
		// Find all orders with free shipping service
		$conditions = array(
			'promo_discount' => array('$exists' => true),
			'promo_code' => array('$exists' => true),
			'promo_discount_bak' => array('$exists' => false),
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
			// Prior to the March 4th promo disaster the calculation was different
			if ($order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00"))
				continue;
			
			// $x becomes the total after ALL possible discounts, this includes the 10off50 service and credits
			$x = $order['subTotal'] + $order['promo_discount'];
			if (isset($order['discount']))
				$x += $order['discount'];
			if (isset($order['credit_used']))
				$x += $order['credit_used'];
			
			// if $x is < 0 that means the full value of promo_discount was not used
			if (number_format($x,2, '.', '') < 0) {
				$i++;
				
				// the amount that was actually used is stored in $promo_actual
				$promo_actual = $order['promo_discount'] - $x;
				
				// Add promo_actual to order record
				$order = Order::find($order['_id']);
				$order->promo_discount_bak = $order['promo_discount'];
				$order->promo_discount = $promo_actual;
				$order->save();
			}
		}

		$this->out('Fixed ' . $i . ' order records.');
	}
}