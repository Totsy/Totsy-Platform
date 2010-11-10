<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoCode;

class DashboardController extends \lithium\action\Controller {

	public function index() {
		$conditions = array(
			'date_created' => array(
				'$gte' => new MongoDate(strtotime('-6 days - 4 hours')))
		);
		$weekRevenue = $this->_revenue(array('conditions' => $conditions), array('dateGroup' => 'getDay'));
		$conditions = array(
			'created_date' => array(
				'$gte' => new MongoDate(strtotime('-6 days - 4 hours')))
		);
		$weekRegistrations = $this->_registrations(array('conditions' => $conditions), array('dateGroup' => 'getDay'));
		$conditions = array(
			'date_created' => array(
				'$gte' => new MongoDate(strtotime('-4 month - 4 hours'))
		));
		$monthsRevenue = $this->_revenue(array('conditions' => $conditions));
		$conditions = array(
			'created_date' => array(
				'$gte' => new MongoDate(strtotime('-4 month - 4 hours'))
		));
		$monthsRegistrations = $this->_registrations(array('conditions' => $conditions));

		return compact('weekRevenue', 'monthsRevenue', 'weekRegistrations', 'monthsRegistrations');
	}

	protected function _revenue($params, array $options = array('dateGroup' => 'getMonth')) {
		$conditions = $params['conditions'];
		$date = $options['dateGroup'];
		$keys = new MongoCode("function(doc){return {'date': doc.date_created.$date()}}");
		$inital = array(
			'total' => 0,
			'count' => 0,
			'tax' => 0,
			'handling' => 0
		);
		$reduce = new MongoCode('function(doc, prev){ 
			prev.total += doc.total,
			prev.count += 1,
			prev.tax += doc.tax,
			prev.handling += doc.handling}'
		);
		$collection = Order::collection();
		return $collection->group($keys, $inital, $reduce, $conditions);
	}

	public function _registrations($params, array $options = array('dateGroup' => 'getMonth')) {
		$conditions = $params['conditions'];
		$date = $options['dateGroup'];
		$keys = new MongoCode("function(doc){return {'date': doc.created_date.$date()}}");
		$inital = array(
			'count' => 0
		);
		$reduce = new MongoCode('function(doc, prev){ 
			prev.count += 1}'
		);
		$collection = User::collection();
		return $collection->group($keys, $inital, $reduce, $conditions);
	}


}

?>