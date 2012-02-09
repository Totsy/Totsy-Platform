<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use admin\models\Dashboard;
use MongoDate;
use MongoCode;
use FusionCharts;

class DashboardController extends BaseController {

	/**
	 * Generates the dashboard report of Totsy's revenue and registration
	 * @see
	 */
    public function index() {
		/**
		 * Build a MongoDB group call for the monthly revenue
		 * numbers.
		 */

		ini_set("display_errors", 1 );

		$collection = Dashboard::collection();
		$keys = new MongoCode("
			function(doc){
				return {
					'month': doc.date.getMonth(),
					'year': doc.date.getFullYear(),
					'type' : doc.type
				}
			}"
		);
		$inital = array(
			'total' => 0
		);
		$reduce = new MongoCode('function(doc, prev){
				prev.total += doc.total
			}'
		);
		$date = array(
			'date' => array(
				'$gte' => new MongoDate(mktime(0, 0, 0, date("m") - 6, 1, date("Y"))),
				'$lt' => new MongoDate(mktime(0, 0, 0, date("m"), 1, date("Y")))
		));

		$summary = $collection->group($keys, $inital, $reduce, $date);
		$conditions = $date + array('type' => 'registration');
		$registrationDetails = Dashboard::find('all', compact('conditions'));
		$conditions = $date + array('type' => 'revenue');
		$revenuDetails = Dashboard::find('all', compact('conditions'));
		$conditions = $date + array('type' => 'gross');
		$grossDetails = Dashboard::find('all', compact('conditions'));
		/**
		 * BUild the chart functionality.
		 */
		$MonthComboChart = new FusionCharts("MSColumn3DLineDY","800","350");

	    # Store chart attributes in a variable
		$params = array(
			'caption=Monthly Revenue and Registrations',
			'subcaption=Comparision',
			'xAxisName=Month',
			'pYAxisName=Revenue',
			'sYAxisName=Total Registrations',
			'decimalPrecision=0'
		);
	    $MonthComboChart->setChartParams(implode(';', $params));
		$monthList = array();
		foreach ($summary['retval'] as $data) {
			if (!in_array($data['month'], $monthList)) {
				$monthList[] = $data['month'];
				$date = mktime(0, 0, 0, $data['month'] + 1, 1, $data['year']);
				$dates[$date] = date('F', mktime(0, 0, 0, $data['month'] + 1, 1, $data['year']));
			}
		}
		foreach ($summary['retval'] as $data) {
			$date = mktime(0, 0, 0, $data['month'] + 1, 1, $data['year']);
			if ($data['type'] == 'revenue') {
				$revenue[$date] = $data['total'];
			} else if ($data['type'] == 'registration'){
				$registrations[$date] = $data['total'];
			} else {
			    $gross[$date] = $data['total'];
			}
		}

		ksort($dates);
		ksort($revenue);
		ksort($registrations);
		ksort($gross);
		$chartData[0][0] = "Gross Revenue";
		$chartData[0][1] = "numberPrefix=$;showValues=1";
		foreach ($gross as $key => $value) {
			$chartData[0][] = $value;
		}
		$chartData[1][0] = "Registrations";
		$chartData[1][1] = "parentYAxis=S";
		foreach ($registrations as $key => $value) {
			$chartData[1][] = $value;
		}
		$MonthComboChart->addChartDataFromArray($chartData, $dates);
		/**
		 * Build chart data for net revenue
		 */
		$RevenueChart = new FusionCharts("MSArea2D","800","350");
		$currentMonthDesc = date('F', time());
		$lastMonthDesc = date('F', strtotime('last month'));
		$params = array(
			'caption=Daily Net Revenue',
			"subcaption=For the Month of $currentMonthDesc",
			'xAxisName=Day of Month',
			'numberPrefix=$',
			'showValues=0'
		);
	    $RevenueChart->setChartParams(implode(';', $params));
		$currentMonth = $this->monthData(array('group' => 1));
		$lastMonth = $this->monthData(array(
			'range' => true,
			'min' => -1,
			'max' => 0,
			'group' => 0
		));
		$lastMonth['revenue'][0] = array_slice(
			$lastMonth['revenue'][0],
			0,
			count($currentMonth['dates']),
			true
		);
		$revenue = (is_array($currentMonth['revenue'])) ? $lastMonth['revenue'] + $currentMonth['revenue']:$lastMonth['revenue'];
		$revenue[0][0] = "$lastMonthDesc Revenue";
		$revenue[0][1] = 'lineThickness=.5';
		$revenue[1][0] = "$currentMonthDesc Revenue";
		$revenue[1][1] = 'lineThickness=5';
		ksort($revenue[0]);
		ksort($revenue[1]);
		$RevenueChart->addChartDataFromArray($revenue, $currentMonth['dates']);

		/**
		* Build chart for gross revenue
		**/

		$GrossRevChart = new FusionCharts("MSArea2D","800","350");
		$currentMonthDesc = date('F', time());
		$lastMonthDesc = date('F', strtotime('last month'));
		$params = array(
			'caption=Daily Gross Revenue',
			"subcaption=For the Month of $currentMonthDesc",
			'xAxisName=Day of Month',
			'numberPrefix=$',
			'showValues=0'
		);
	    $GrossRevChart->setChartParams(implode(';', $params));
		$currentMonth = $this->monthData(array('group' => 1));
		$lastMonth['gross'][0] = array_slice(
			$lastMonth['gross'][0],
			0,
			count($currentMonth['dates']),
			true
		);
		$gross = (is_array($currentMonth['gross'])) ? $lastMonth['gross'] + $currentMonth['gross']:$lastMonth['gross'];
		$gross[0][0] = "$lastMonthDesc Revenue";
		$gross[0][1] = 'lineThickness=.5';
		$gross[1][0] = "$currentMonthDesc Revenue";
		$gross[1][1] = 'lineThickness=5';
		ksort($gross[0]);
		ksort($gross[1]);
		$GrossRevChart->addChartDataFromArray($gross, $currentMonth['dates']);
		/**
		* Build chart for registration
		**/
		$RegChart = new FusionCharts("MSArea2D","800","350");
		$params = array(
			'caption=Daily Regsistration',
			"subcaption=For the Month as of $currentMonthDesc ",
			'xAxisName=Day of Month',
			'showValues=0'
		);
		$RegChart->setChartParams(implode(';', $params));
		$lastMonth['registration'][0] = array_slice(
			$lastMonth['registration'][0],
			0,
			count($currentMonth['dates']),
			true
		);
		$registration = $lastMonth['registration'] + $currentMonth['registration'];

		$registration[0][0] = "$lastMonthDesc Registrations";
		$registration[0][1] = 'lineThickness=.5';
		$registration[1][0] = "$currentMonthDesc Registrations";
		$registration[1][1] = 'lineThickness=5';
		ksort($registration[0]);
		ksort($registration[1]);
		$RegChart->addChartDataFromArray($registration, $currentMonth['dates']);
		$yearToDate = $this->yearToDate();
		$updateTime = max(array_keys($currentMonth['dates']));
		return compact(
			'updateTime',
			'summary',
			'currentMonth',
			'lastMonth',
			'registrationDetails',
			'revenuDetails',
			'MonthComboChart',
			'RevenueChart',
			'GrossRevChart',
			'RegChart',
			'currentMonthDesc',
			'lastMonthDesc',
			'yearToDate'
		);
    }

	public function monthData(array $options = array()) {
		$range = (empty($options['range'])) ? false : true;
		$options['min'] = (empty($options['min'])) ? 0 : $options['min'];
		$options['group'] = (empty($options['group'])) ? 0 : $options['group'];
		if ($range == true) {
			$conditions = array(
				'date' => array(
					'$gte' => new MongoDate(mktime(0, 0, 0, date("m") + $options['min'], 1, date("Y"))),
					'$lte'=> new MongoDate(mktime(0, 0, 0, date("m") + $options['max'], 0, date("Y")))
			));
		} else {
			$conditions = array(
				'date' => array(
					'$gte' => new MongoDate(mktime(0, 0, 0, date("m") + $options['min'], 1, date("Y")))
			));
		}
		$current = Dashboard::find('all', compact('conditions'));
		$current = $current->data();
		$dateList = array();
		$dates = array();
		$currentRevenue = array();
		$currentGrossRev = array();
		$currentReg = array();
		foreach ($current as $data) {
			if (!in_array($data['date'], $dateList)) {
				$dateList[] = $data['date'];
				$dates[$data['date']] = date('d', $data['date']);
			}
		}
		foreach ($current as $record) {
			if ($record['type'] == 'revenue') {
				$currentRevenue[$record['date']] = $record['total'];
			} else if ($record['type'] == 'gross'){
			    $currentGrossRev[$record['date']] = $record['total'];
			} else {
				$currentReg[$record['date']] = $record['total'];
			}
		}
		ksort($dates);
		ksort($currentRevenue);
		ksort($currentGrossRev);
		ksort($currentReg);
		$registration = array();
		$i = 2;
		foreach ($currentGrossRev as $key => $value) {
			$gross[$options['group']][$i] = $value;
			++$i;
		}
		$i = 2;
		foreach ($currentRevenue as $key => $value) {
			$revenue[$options['group']][$i] = $value;
			++$i;
		}
		$i = 2;
		foreach ($currentReg as $key => $value) {
			$registration[$options['group']][$i] = $value;
			++$i;
		}
		return compact('dates', 'revenue', 'registration', 'gross');
	}

	public function yearToDate() {
		$collection = Dashboard::collection();
		$keys = new MongoCode("
			function(doc){
				return {
					'type' : doc.type
				}
			}"
		);
		$inital = array(
			'total' => 0
		);
		$reduce = new MongoCode('function(doc, prev){
				prev.total += doc.total
			}'
		);
		$date = array(
			'date' => array(
				'$gte' => new MongoDate(strtotime('January')),
				'$lt' => new MongoDate()
		));

		$summary = $collection->group($keys, $inital, $reduce, $date);
		return $summary['retval'];
	}
}

?>
