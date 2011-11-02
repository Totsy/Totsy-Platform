<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\models\Order;
use admin\models\Item;
use admin\models\Promocode;
use admin\models\Dashboard;
use MongoDate;
use MongoRegex;
use MongoId;

/**
 * Calculate promo code details to be displayed in the admin
 */
class CreatePromocodeDetail extends \lithium\console\Command {

	/**
	 * The environment to use when running the command. db='development' is the default.
	 * Set to 'production' if running live when using a cronjob.
	 */
	public $env = 'development';
	public $beginning = "Today";
	public $end = "Now";

	public function run() {
		$this->header('Launching Create Promocode Detail....');
		Environment::set($this->env);
		$this->_fix();
		$this->out('Finished Create Promocode Detail');
	}

	protected function _fix() {

		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate  = new MongoDate(strtotime($this->end));
		
		// Find all orders that use a promo code
		$conditions = array(
			'subTotal' => array('$type' => 1),
			'promo_discount' => array('$exists' => true),
			'promo_code' => array('$exists' => true),
			'date_created' => array(
				'$gte' => $startDate,
				'$lt' => $endDate)
		);
		$orders = Order::find('all', array('conditions' => $conditions));//, 'limit' => 10
		
		/* 
		 * 
		 */
		$promocode_detail = array();
		foreach ($orders as $order) {
			
			$current_promocode = null;
			$order = $order->data();
			// Skip bad orders...except August and later, we'll count all of those
			if ($order['date_created']['sec'] < strtotime("August 01, 2011 00:00:00")) {
				
				$total = $order['total'];
				$calc_total = $order['subTotal'];
				
				if (isset($order['promo_discount'])) {
					if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00")) {
						$calc_total += $order['promo_actual'];
					} else {
						if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
							$calc_total -= $order['promo_discount'];
						else
							$calc_total += $order['promo_discount'];
					}
				}
				if (isset($order['discount'])) {
					if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
						$calc_total -= $order['discount'];
					else
						$calc_total += $order['discount'];
				}
				if (isset($order['credit_used'])) {
					if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
						$calc_total -= $order['credit_used'];
					else
						$calc_total += $order['credit_used'];
				}
				
				$calc_total += $order['handling'] + $order['tax'];
				
				if (isset($order['overSizeHandling']))
					$calc_total += $order['overSizeHandling'];
				
				if (number_format($calc_total,2, '.', '') != number_format($total,2, '.', '')) {
					$this->out("skipping bad order: ".$order["_id"]);
					continue;
				}
			}
			
			$order_date = date("Y-m-d",$order['date_created']['sec']);
			$promocode = Promocode::find('first', array('conditions' => array('code' => array('like' => '/^'.$order["promo_code"].'$/i'))));
			
			// Some promo codes don't exist in the promocodes table so we just use the data available in the order record
			if ($promocode==null) {
				$order["promo_code"] = strtoupper($order["promo_code"]);
				$current_promocode = $order["promo_code"];
				if (!isset($promocode_detail[$order_date][$order["promo_code"]])) {
					$promocode_detail[$order_date][$order["promo_code"]]['amount_saved'] = 0;
					$promocode_detail[$order_date][$order["promo_code"]]['number_used'] = 0;
					$promocode_detail[$order_date][$order["promo_code"]]['date_string'] = $order_date;
					$promocode_detail[$order_date][$order["promo_code"]]['code'] = $order["promo_code"];
					$promocode_detail[$order_date][$order["promo_code"]]['code_id'] = "unknown";
					if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
						$promocode_detail[$order_date][$order["promo_code"]]['value'] = -$order['promo_discount'];
					else
						$promocode_detail[$order_date][$order["promo_code"]]['value'] = $order['promo_discount'];
					$promocode_detail[$order_date][$order["promo_code"]]['type'] = "unknown";
				}
				
				if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00"))
					$promocode_detail[$order_date][$order["promo_code"]]['amount_saved'] += $order['promo_actual'];
				else {
					if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
						$promocode_detail[$order_date][$order["promo_code"]]['amount_saved'] -= $order['promo_discount'];
					else
						$promocode_detail[$order_date][$order["promo_code"]]['amount_saved'] += $order['promo_discount'];
				}
				
				$promocode_detail[$order_date][$order["promo_code"]]['number_used']++;
				
			} else {
				
				$promocode = $promocode->data();
				
				// "special" promo codes have a parent code, everything gets rolled up to the parent
				if (isset($promocode['special'])) {
					$parent_promocode = Promocode::find('first', array('conditions' => array('_id' => new MongoId($promocode['parent_id']))));
					$parent_promocode = $parent_promocode->data();
					
					$current_promocode = $parent_promocode['code'];
					
					if (!isset($promocode_detail[$order_date][$parent_promocode['code']])) {
						$promocode_detail[$order_date][$parent_promocode['code']]['amount_saved'] = 0;
						$promocode_detail[$order_date][$parent_promocode['code']]['number_used'] = 0;
						$promocode_detail[$order_date][$parent_promocode['code']]['date_string'] = $order_date;
						$promocode_detail[$order_date][$parent_promocode['code']]['code'] = $parent_promocode['code'];
						$promocode_detail[$order_date][$parent_promocode['code']]['code_id'] = $parent_promocode['_id'];
						$promocode_detail[$order_date][$parent_promocode['code']]['value'] = $promocode['discount_amount'];
						$promocode_detail[$order_date][$parent_promocode['code']]['type'] = $promocode['type'];
					}
					
					if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00"))
						$promocode_detail[$order_date][$parent_promocode['code']]['amount_saved'] += $order['promo_actual'];
					else {
						if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
							$promocode_detail[$order_date][$parent_promocode['code']]['amount_saved'] -= $order['promo_discount'];
						else
							$promocode_detail[$order_date][$parent_promocode['code']]['amount_saved'] += $order['promo_discount'];
					}
					
					$promocode_detail[$order_date][$parent_promocode['code']]['number_used']++;
				} else {
					
					$current_promocode = $promocode['code'];
					
					if (!isset($promocode_detail[$order_date][$promocode['code']])) {
						$promocode_detail[$order_date][$promocode['code']]['amount_saved'] = 0;
						$promocode_detail[$order_date][$promocode['code']]['number_used'] = 0;
						$promocode_detail[$order_date][$promocode['code']]['date_string'] = $order_date;
						$promocode_detail[$order_date][$promocode['code']]['code'] = $promocode['code'];
						$promocode_detail[$order_date][$promocode['code']]['code_id'] = $promocode['_id'];
						$promocode_detail[$order_date][$promocode['code']]['value'] = $promocode['discount_amount'];
						$promocode_detail[$order_date][$promocode['code']]['type'] = $promocode['type'];
					}
					
					if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00"))
						$promocode_detail[$order_date][$promocode['code']]['amount_saved'] += $order['promo_actual'];
					else {
						if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
							$promocode_detail[$order_date][$promocode['code']]['amount_saved'] -= $order['promo_discount'];
						else
							$promocode_detail[$order_date][$promocode['code']]['amount_saved'] += $order['promo_discount'];
					}
					
					$promocode_detail[$order_date][$promocode['code']]['number_used']++;
				}
			
			}
			
			// Calculate Gross Revenue
			$gross_total = 0;
			
			$gross_total = $order['subTotal'] + $order['handling'] + $order['tax'];
			
			if (isset($order['overSizeHandling']))
				$gross_total += $order['overSizeHandling'];
			if (isset($order['handlingDiscount']))
				$gross_total += $order['handlingDiscount'];
			if (isset($order['overSizeHandlingDiscount']))
				$gross_total += $order['overSizeHandlingDiscount'];
				
			if (!isset($promocode_detail[$order_date][$current_promocode]['gross']))
				$promocode_detail[$order_date][$current_promocode]['gross'] = 0;
			$promocode_detail[$order_date][$current_promocode]['gross'] += $gross_total;
			
			// Calculate Net Revenue
			$net_total = $order['subTotal'];
			
			if (isset($order['promo_discount'])) {
				if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00")) {
					$net_total += $order['promo_actual'];
				} else {
					if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
						$net_total -= $order['promo_discount'];
					else
						$net_total += $order['promo_discount'];
				}
			}
			if (isset($order['discount']) && abs($order['discount'])==10) {
				if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
					$net_total -= $order['discount'];
				else
					$net_total += $order['discount'];
			}
			if (isset($order['credit_used'])) {
				if ($order['date_created']['sec'] > strtotime("October 06, 2011 05:57:00"))
					$net_total -= $order['credit_used'];
				else
					$net_total += $order['credit_used'];
			}
			
			$net_total += $order['handling'] + $order['tax'];
			
			if (isset($order['overSizeHandling']))
				$net_total += $order['overSizeHandling'];
			
			if (!isset($promocode_detail[$order_date][$current_promocode]['net']))
				$promocode_detail[$order_date][$current_promocode]['net'] = 0;
			$promocode_detail[$order_date][$current_promocode]['net'] += $net_total;
			
		}
		
		$DashCollection = Dashboard::collection();
		foreach($promocode_detail as $date => $codes) {
			$total_amount = 0;
			$total_count = 0;
			foreach ($codes as $code) {
				$total_amount += $code['amount_saved'];
				$total_count += $code['number_used'];
			}
			
			$details['date_string'] = $date;
			$details['date'] = new MongoDate(strtotime($date));
			$details['type'] = 'promocodes';
			$details['codes'] = $codes;
			$details['total_amount'] = $total_amount;
			$details['total_count'] = $total_count;
			$condition = array('date' => $date, 'type' => 'promocodes');
			$DashCollection->update($condition, $details, array('upsert' => true));
		}
	}
}