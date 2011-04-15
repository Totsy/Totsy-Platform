<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use admin\models\Dashboard;
use MongoDate;
use MongoCode;
use FusionCharts;

class DashboardController extends \lithium\action\Controller {

    public function index() {
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
				'$gte' => new MongoDate(mktime(0, 0, 0, date("m") - 3, 1, date("Y"))),
				'$lte' => new MongoDate()
		));

		$summary = $collection->group($keys, $inital, $reduce, $date);
		$conditions = $date + array('type' => 'registration');
		$registrationDetails = Dashboard::find('all', compact('conditions'));
		$conditions = $date + array('type' => 'revenue');
		$revenuDetails = Dashboard::find('all', compact('conditions'));

		$FC = new FusionCharts("MSColumn3DLineDY","900","500");

	    # Store chart attributes in a variable
		$params = array(
			'caption=Monthly Revenue and Registrations',
			'subcaption=Comparision',
			'xAxisName=Month',
			'pYAxisName=Revenue',
			'sYAxisName=Total Registrations',
			'decimalPrecision=0'
		);
	    $FC->setChartParams(implode(';', $params));
		$monthList = array();
		$i = 0;
		foreach ($summary['retval'] as $data) {
			if (!in_array($data['month'], $monthList)) {
				$monthList[] = $data['month'];
				$dates[] = date('F', mktime(0, 0, 0, $data['month'] + 1, 0, $data['year']));
			}
		}
		$i = 2;
		foreach ($summary['retval'] as $data) {
			if ($data['type'] == 'revenue') {
				$chartData[0][0] = "Revenue";
				$chartData[0][1] = "numberPrefix=$;showValues=1";
				$chartData[0][$i] = $data['total'];
				++$i;
			}
		}
		$i = 2;
		foreach ($summary['retval'] as $data) {
			if ($data['type'] == 'registration') {
				$chartData[1][0] = "Registrations";
				$chartData[1][1] = "parentYAxis=S";
				$chartData[1][$i] = $data['total'];
				++$i;
			}
		}
		$FC->addChartDataFromArray($chartData, $dates);

		return compact('summary', 'registrationDetails', 'revenuDetails', 'FC');
    }
}

?>