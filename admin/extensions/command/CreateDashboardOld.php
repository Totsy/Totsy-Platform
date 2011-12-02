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
class CreateDashboardOld extends \lithium\console\Command  {

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
	public $beginning = "2009-10-08";
	public $end = "2010-08-03";

	/**
	 * This is a one off script used to dump revenue data for orders that were migrated from the old system.
	 * @see docs/admin/controllers/DashboardController
	 */
	public function run() {
	    MongoCursor::$timeout = 100000;
		Environment::set($this->env);
		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate = new MongoDate(strtotime($this->end));

		$OrdCollection = Order::collection();
		$keys = new MongoCode("
			function(doc){
				return {
					'date': doc.date_created.toDateString(),
				}
			}"
		);
		
		$conditions = array(
			'date_created' => array(
				'$gte' => $startDate,
				'$lte' => $endDate)
		);
		
		$initialGross = array(
			'total' => 0,
			'calcTotal' => 0,
			'subTotal' => 0,
			'handling' => 0,
			'tax' => 0,
			'count' => 0
		);
		$reduceGross = new MongoCode('function(doc, prev){
				
				prev.count++;
				prev.calcTotal += 
					(Number(doc.subtotal) + 
					Number(doc.handling) + 
					Number(doc.tax));
				
				prev.subTotal += Number(doc.subtotal);
				prev.handling += Number(doc.handling);
				prev.tax += Number(doc.tax);
				prev.total += Number(doc.total);
				
			}'
		);
		
		$grossRevenueDetail = $OrdCollection->group($keys, $initialGross, $reduceGross, $conditions);
		
		$DashCollection = Dashboard::collection();

		foreach ($grossRevenueDetail['retval'] as $details) {
			$details['date_string'] = date("Y-m-d",strtotime($details['date']));
			$details['date'] = new MongoDate(strtotime($details['date']));
			$details['type'] = 'gross';
			$condition = array('date' => $details['date'], 'type' => $details['type']);
			$DashCollection->update($condition, $details, array('upsert' => true));
		}

	}

}

