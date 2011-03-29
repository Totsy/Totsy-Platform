<?php

namespace admin\controllers;

use admin\models\Order;
use admin\models\User;
use admin\models\Dashboard;
use MongoDate;
use MongoCode;

class DashboardController extends \lithium\action\Controller {

    public function index() {
    $conditions = array(
      'created_date' => array('$gt' => new MongoDate(strtotime('-5min')))
    );
      $record = Dashboard::find('first', compact('conditions'));
    if ($record) {
      $record = $record->data();
    }
    return $record;
    }
}
//* 

?>