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

	/**
	 * Generate data to be used for the dashbaord.  Gather data about net reveue, gross revenue,
	 * and registration.
	 * @see docs/admin/controllers/DashboardController
	 */
	public function run() {
	    MongoCursor::$timeout = 100000;
		Environment::set($this->env);
		$startDate  = new MongoDate(strtotime($this->beginning));
		$endDate = new MongoDate();
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
		$inital = array(
			'total' => 0
		);
		$reduce = new MongoCode('function(doc, prev){
				prev.total += Number(doc.total)
			}'
		);
		$conditions = array(
			'date_created' => array(
				'$gte' => $startDate,
				'$lte' => $endDate
		));
		$reduceGross = new MongoCode('function(doc, prev){
				prev.total += (Number(doc.subTotal) + Number(doc.handling) + Number(doc.tax));

				if (doc.promo_discount != null) {
				    prev.total += (Number(doc.promo_discount)  * -1);
				}
				if (doc.credit_used != null) {
				    prev.total += (Number(doc.credit_used)  * -1);
				}

			}'
		);
		$revenueDetail = $OrdCollection->group($keys, $inital, $reduce, $conditions);
		$grossRevenueDetail = $OrdCollection->group($keys, $inital, $reduceGross, $conditions);
		$DashCollection = Dashboard::collection();
		foreach ($regDetails['retval'] as $details) {
			$details['date'] = new MongoDate(strtotime($details['date']));
			$details['type'] = 'registration';
			$condition = array('date' => $details['date'], 'type' => $details['type']);
			$DashCollection->update($condition, $details, array('upsert' => true));
		}

		foreach ($revenueDetail['retval'] as $details) {
			$details['date'] = new MongoDate(strtotime($details['date']));
			$details['type'] = 'revenue';
			$condition = array('date' => $details['date'], 'type' => $details['type']);
			$DashCollection->update($condition, $details, array('upsert' => true));
		}
		foreach ($grossRevenueDetail['retval'] as $details) {
			$details['date'] = new MongoDate(strtotime($details['date']));
			$details['type'] = 'gross';
			$condition = array('date' => $details['date'], 'type' => $details['type']);
			$DashCollection->update($condition, $details, array('upsert' => true));
		}

	}

}

