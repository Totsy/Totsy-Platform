<?php

namespace admin\extensions\command;

use admin\models\Order;
use admin\models\User;
use admin\models\Dashboard;
use MongoDate;
use MongoCode;
use lithium\analysis\Logger;
use lithium\core\Environment;
use MongoCursor;
use MongoId;

/**
 * Li3 Command to Create Dashboard Data.
 * @todo Improve documentation
 */
class CreateDashboard extends \lithium\console\Command  {

	public $verbose = false;

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	* Set when the data should start calculating
	**/
	public $beginning = "Today";
	public $end = "Now";

	/**
	 * Generate data to be used for the dashbaord.  Gather data about net reveue, gross revenue,
	 * and registration.
	 * @see docs/admin/controllers/DashboardController
	 */
	public function run() {
	    MongoCursor::$timeout = 100000;
		Environment::set($this->env);
		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate = new MongoDate(strtotime($this->end));
		$collection = User::collection();
		$keys = new MongoCode("
			function(doc){
				return {
					'date': doc.created_date.toDateString(),
				}
			}"
		);
		$inital = array(
			'total' => 0
		);
		$reduce = new MongoCode('function(doc, prev){
				prev.total += 1
			}'
		);
		$conditions = array(
			'created_date' => array(
				'$gte' => $startDate,
				'$lt' => $endDate
		));

		$regDetails = $collection->group($keys, $inital, $reduce, $conditions);

		$OrdCollection = Order::collection();
		$keys = new MongoCode("
			function(doc){
				return {
					'date': doc.date_created.toDateString(),
				}
			}"
		);
		
		$conditions = array(
			'subTotal' => array('$type' => 1),
			'date_created' => array(
				'$gte' => $startDate,
				'$lte' => $endDate)
		);
		
		$initialNet = array(
			'total' => 0,
			'subTotal' => 0,
			'product' => 0,
			'handling' => 0,
			'overSizeHandling' => 0,
			'handling_total' => 0,
			'fs_service' => 0,
			'fs_promo' => 0,
			'tax' => 0,
			'promo_discount' => 0,
			'discount' => 0,
			'credit_used' => 0,
			'calc_total' => 0,
			'count' => 0,
			'skippedOrderCount' => 0,
			'skippedOrderTotal' => 0
		);
		$reduceNet = new MongoCode('function(doc, prev){
				
				current_total = Number(doc.subTotal);
				
				if (doc.promo_discount != null && doc.promo_type != "free_shipping") {
					if(doc.promo_actual != null && doc.date_created > new Date("March 04, 2011 12:34:00")) {
						current_total += Number(doc.promo_actual);
					} else {
						if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
							current_total -= Number(doc.promo_discount);
						}
						else
						{
							current_total += Number(doc.promo_discount);
						}
					}
				}
				if (doc.discount != null) {
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						current_total -= Number(doc.discount);
					}
					else
					{
						current_total += Number(doc.discount);
					}
				}
				if (doc.credit_used != null) {
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						current_total -= Number(doc.credit_used);
					}
					else
					{
						current_total += Number(doc.credit_used);
					}
				}
				
				if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
					if (doc.handlingDiscount != null) {
						current_total -= Number(doc.handlingDiscount);
					}
					if (doc.overSizeHandlingDiscount != null) {
						current_total -= Number(doc.overSizeHandlingDiscount);
					}
				}
				
				current_total += 
					(Number(doc.handling) + 
					Number(doc.tax));
					
				if (doc.overSizeHandling != null) {
					current_total += Number(doc.overSizeHandling);
				}
				
				if (doc.date_created < new Date("August 01, 2011 00:00:00") && 
					current_total.toFixed(2) != Number(doc.total.toFixed(2))) {
					prev.skippedOrderCount++;
					prev.skippedOrderTotal += Number(doc.total);
					return;
				}
				
				prev.count++;
				prev.subTotal += Number(doc.subTotal);
				prev.product += Number(doc.subTotal);
				if(doc.date_created > new Date("October 06, 2011 05:57:00") && doc.handlingDiscount != null) {
					prev.handling += Number(doc.handling) - Number(doc.handlingDiscount);
					prev.handling_total += Number(doc.handling) - Number(doc.handlingDiscount);
				}
				else
				{
					prev.handling += Number(doc.handling);
					prev.handling_total += Number(doc.handling);
				}
				
				if (doc.overSizeHandling != null) {
					if(doc.date_created > new Date("October 06, 2011 05:57:00") && doc.overSizeHandlingDiscount != null) {
						prev.overSizeHandling += Number(doc.overSizeHandling) - Number(doc.overSizeHandlingDiscount);
						prev.handling_total += Number(doc.overSizeHandling) - Number(doc.overSizeHandlingDiscount);
					}
					else
					{
						prev.overSizeHandling += Number(doc.overSizeHandling);
						prev.handling_total += Number(doc.overSizeHandling);
					}
				}
				prev.tax += Number(doc.tax);
				
				prev.total += Number(doc.total);
				
				if (doc.promo_discount != null && doc.promo_type != "free_shipping") {
					if(doc.promo_actual != null && doc.date_created > new Date("March 04, 2011 12:34:00")) {
						prev.promo_discount += Number(doc.promo_actual);
						prev.product += Number(doc.promo_actual);
					} else {
						if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
							prev.promo_discount -= Number(doc.promo_discount);
							prev.product -= Number(doc.promo_discount);
						}
						else
						{
							prev.promo_discount += Number(doc.promo_discount);
							prev.product += Number(doc.promo_discount);
						}
					}
				}
				if (doc.discount != null && Math.abs(doc.discount) == 10) {
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						prev.discount -= Number(doc.discount);
						prev.product -= Number(doc.discount);
					}
					else
					{
						prev.discount += Number(doc.discount);
						prev.product += Number(doc.discount);
					}
				}
				if (doc.credit_used != null) {
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						prev.credit_used -= Number(doc.credit_used);
						prev.product -= Number(doc.credit_used);
					}
					else
					{
						prev.credit_used += Number(doc.credit_used);
						prev.product += Number(doc.credit_used);
					}
				}
				
				if (doc.handlingDiscount != null) {
					if (doc.service == "freeshipping")
						prev.fs_service += Number(doc.handlingDiscount);
					else
						prev.fs_promo += Number(doc.handlingDiscount);
				}
				if (doc.overSizeHandlingDiscount != null) {
					if (doc.service == "freeshipping")
						prev.fs_service += Number(doc.overSizeHandlingDiscount);
					else
						prev.fs_promo += Number(doc.overSizeHandlingDiscount);
				}
					
				prev.calc_total += current_total;
				
			}'
		);
		
		$initialGross = array(
			'total' => 0,
			'subTotal' => 0,
			'handling' => 0,
			'overSizeHandling' => 0,
			'handling_total' => 0,
			'tax' => 0,
			'service_handling_discount' => 0,
			'service_overSizeHandling_discount' => 0,
			'promo_handling_discount' => 0,
			'promo_overSizeHandling_discount' => 0,
			'count' => 0,
			'skippedOrderCount' => 0,
			'skippedOrderTotal' => 0
		);
		$reduceGross = new MongoCode('function(doc, prev){
				
				current_total = Number(doc.subTotal);
				
				if (doc.promo_discount != null) {
					if(doc.promo_actual != null && doc.date_created > new Date("March 04, 2011 12:34:00")) {
						current_total += Number(doc.promo_actual);
					} else {
						if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
							current_total -= Number(doc.promo_discount);
						}
						else
						{
							current_total += Number(doc.promo_discount);
						}
					}
				}
				if (doc.discount != null) {
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						current_total -= Number(doc.discount);
					}
					else
					{
						current_total += Number(doc.discount);
					}
				}
				if (doc.credit_used != null) {
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						current_total -= Number(doc.credit_used);
					}
					else
					{
						current_total += Number(doc.credit_used);
					}
				}
				
				current_total += 
					(Number(doc.handling) + 
					Number(doc.tax));
					
				if (doc.overSizeHandling != null) {
					current_total += Number(doc.overSizeHandling);
				}
				
				if (doc.date_created < new Date("August 01, 2011 00:00:00") && 
					current_total.toFixed(2) != Number(doc.total.toFixed(2))) {
					prev.skippedOrderCount++;
					prev.skippedOrderTotal += Number(doc.total);
					return;
				}
				
				prev.count++;
				prev.total += 
					(Number(doc.subTotal) + 
					Number(doc.handling) + 
					Number(doc.tax));
				
				prev.subTotal += Number(doc.subTotal);
				prev.handling += Number(doc.handling);
				prev.handling_total += Number(doc.handling);
				prev.tax += Number(doc.tax);
				
				if (doc.overSizeHandling != null) {
					prev.overSizeHandling += Number(doc.overSizeHandling);
					prev.handling_total += Number(doc.overSizeHandling);
					prev.total += Number(doc.overSizeHandling);
				}
				if (doc.handlingDiscount != null) {
					if (doc.service == "freeshipping")
						prev.service_handling_discount += Number(doc.handlingDiscount);
					else
						prev.promo_handling_discount += Number(doc.handlingDiscount);
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						prev.handling -= Number(doc.handlingDiscount);
					}
					else
					{
						prev.handling_total += Number(doc.handlingDiscount);
						prev.total += Number(doc.handlingDiscount);
					}
				}
				if (doc.overSizeHandlingDiscount != null) {
					if (doc.service == "freeshipping")
						prev.service_overSizeHandling_discount += Number(doc.overSizeHandlingDiscount);
					else
						prev.promo_overSizeHandling_discount += Number(doc.overSizeHandlingDiscount);
					if(doc.date_created > new Date("October 06, 2011 05:57:00")) {
						prev.overSizeHandling -= Number(doc.overSizeHandlingDiscount);
					}
					else
					{
						prev.handling_total += Number(doc.overSizeHandlingDiscount);
						prev.total += Number(doc.overSizeHandlingDiscount);
					}
				}
				
			}'
		);
		
		$netRevenueDetail = $OrdCollection->group($keys, $initialNet, $reduceNet, $conditions);
		$grossRevenueDetail = $OrdCollection->group($keys, $initialGross, $reduceGross, $conditions);
		
		$DashCollection = Dashboard::collection();
		foreach ($regDetails['retval'] as $details) {
			$details['date_string'] = date("Y-m-d",strtotime($details['date']));
			$details['date'] = new MongoDate(strtotime($details['date']));
			$details['type'] = 'registration';
			$condition = array('date' => $details['date'], 'type' => $details['type']);
			$DashCollection->update($condition, $details, array('upsert' => true));
		}

		foreach ($netRevenueDetail['retval'] as $details) {
			$details['date_string'] = date("Y-m-d",strtotime($details['date']));
			$details['date'] = new MongoDate(strtotime($details['date']));
			$details['type'] = 'revenue';
			$condition = array('date' => $details['date'], 'type' => $details['type']);
			$DashCollection->update($condition, $details, array('upsert' => true));
		}
		foreach ($grossRevenueDetail['retval'] as $details) {
			$details['date_string'] = date("Y-m-d",strtotime($details['date']));
			$details['date'] = new MongoDate(strtotime($details['date']));
			$details['type'] = 'gross';
			$condition = array('date' => $details['date'], 'type' => $details['type']);
			$DashCollection->update($condition, $details, array('upsert' => true));
		}

	}

}

