<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use admin\models\Dashboard;
use MongoDate;
use MongoCode;

class DashboardController extends \lithium\action\Controller {

    public function index() {
		$limit = array(1);
		$order = array('_id' => 'DESC');
    	$record = Dashboard::find('all', compact('order', 'limit'));
		if ($record) {
			$record = $record->data();
		}
		return $record;
	}
}
?>