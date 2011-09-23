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
 * Fix orders with that use promo codes greater than their subtotal
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
			'subTotal' => array('$type' => 1),
			'promo_discount' => array('$exists' => true),
			'promo_code' => array('$exists' => true),
			'date_created' => array(
				'$gte' => $startDate,
				'$lt' => $endDate)
		);
		$orders = Order::find('all', array('conditions' => $conditions));//, 'limit' => 10
		
		
		/* TODO: Rewrite to go through each day, find unique promocodes, then sum values for each promocode
		 * Would be faster except for days where child codes are used.
		 */
		
		$promocode_detail = array();
		foreach ($orders as $order) {
			
			$current_promocode = null;
			$order = $order->data();
			
			//Skip bad orders...
			$total = $order['total'];
			$calc_total = $order['subTotal'];
			
			if (isset($order['promo_discount'])) {
				if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00")) {
					$calc_total += $order['promo_actual'];
				} else {
					$calc_total += $order['promo_discount'];
				}
			}
			if (isset($order['discount']))
				$calc_total += $order['discount'];
			if (isset($order['credit_used']))
				$calc_total += $order['credit_used'];
			
			$calc_total += $order['handling'] + $order['tax'];
			
			if (isset($order['overSizeHandling']))
				$calc_total += $order['overSizeHandling'];
/*			
			if (number_format($calc_total,2, '.', '') != number_format($total,2, '.', '')) {
				$this->out("skipping bad order: ".$order["_id"]);
				continue;
			}
*/			
			$order_date = date("Y-m-d",$order['date_created']['sec']);
			$promocode = Promocode::find('first', array('conditions' => array('code' => array('like' => '/^'.$order["promo_code"].'$/i'))));
			
			// Some promo codes don't exist in the promocodes table so we just use the data available in the order record
			if ($promocode==null) {
				//var_dump($order);
				//$this->out($order["promo_code"]);
				$order["promo_code"] = strtoupper($order["promo_code"]);
				$current_promocode = $order["promo_code"];
				if (!isset($promocode_detail[$order_date][$order["promo_code"]])) {
					$promocode_detail[$order_date][$order["promo_code"]]['amount_saved'] = 0;
					$promocode_detail[$order_date][$order["promo_code"]]['number_used'] = 0;
					$promocode_detail[$order_date][$order["promo_code"]]['date_string'] = $order_date;
					$promocode_detail[$order_date][$order["promo_code"]]['code'] = $order["promo_code"];
					$promocode_detail[$order_date][$order["promo_code"]]['code_id'] = "unknown";
					$promocode_detail[$order_date][$order["promo_code"]]['value'] = $order['promo_discount'];
					$promocode_detail[$order_date][$order["promo_code"]]['type'] = "unknown";
				}
				
				if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00"))
					$promocode_detail[$order_date][$order["promo_code"]]['amount_saved'] += $order['promo_actual'];
				else
					$promocode_detail[$order_date][$order["promo_code"]]['amount_saved'] += $order['promo_discount'];
				
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
				else
					$promocode_detail[$order_date][$parent_promocode['code']]['amount_saved'] += $order['promo_discount'];
				
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
				else
					$promocode_detail[$order_date][$promocode['code']]['amount_saved'] += $order['promo_discount'];
				
				$promocode_detail[$order_date][$promocode['code']]['number_used']++;
			}
			
			}
			
			// Calculate Gross Revenue
			$gross_total = 0;
			
			$gross_total = $order['subTotal'] + $order['handling'] + $order['tax'];
			
			if (isset($order['overSizeHandling']))
				$gross_total += $order['overSizeHandling'];
			if (isset($order['handling_discount']))
				$gross_total += $order['handling_discount'];
			if (isset($order['overSizeHandling_discount']))
				$gross_total += $order['overSizeHandling_discount'];
				
			if (!isset($promocode_detail[$order_date][$current_promocode]['gross']))
				$promocode_detail[$order_date][$current_promocode]['gross'] = 0;
			$promocode_detail[$order_date][$current_promocode]['gross'] += $gross_total;
			
			// Calculate Net Revenue
			$net_total = $order['subTotal'];
			
			if (isset($order['promo_discount'])) {
				if (isset($order['promo_actual']) && $order['date_created']['sec'] > strtotime("March 04, 2011 12:34:00")) {
					$net_total += $order['promo_actual'];
				} else {
					$net_total += $order['promo_discount'];
				}
			}
			if (isset($order['discount']))
				$net_total += $order['discount'];
			if (isset($order['credit_used']))
				$net_total += $order['credit_used'];
			
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
			/*$this->out('Order: ' . $order['_id']);
			$this->out('handling_discount: ' . $handling_discount);
			$this->out('overSizeHandling_discount: ' . $overSizeHandling_discount);
			$this->out('');
			exit;*/
		}

		//$this->out('Fixed ' . $i . ' order records.');
	}
}