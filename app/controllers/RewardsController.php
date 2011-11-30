<?php

namespace app\controllers;

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