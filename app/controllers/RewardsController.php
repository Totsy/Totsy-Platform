<?php

namespace app\controllers;

use app\models\User;
use app\models\Menu;
use app\models\Affiliate;
use lithium\security\Auth;
use lithium\storage\Session;
use MongoDate;
use li3_facebook\extension\FacebookProxy;

use app\extensions\Mailer;
use lithium\action\Request;
use \lithium\data\Connections;
use \lithium\util\Validator;

class RewardsController extends BaseController {

	//This function takes an email (unsubscribed user) from Sailthru and posts it to Unsubcentral's API under the list unsubscribed
	public function index() {
		//to serve either register or view page for 500 point reward program
		
  	}

	public function members(){
		//to serve app via API calls
	}
}

?>