<?php

namespace app\controllers;

use \app\models\MomOfTheWeek;
use \MongoDate;

class MomOfTheWeeksController extends \lithium\action\Controller {

	public function index() {
		$success = false;
		if($this->request->data){
			// Create the sweepstakes entry
			$data = $this->request->data;
			$data['date_created'] = new MongoDate();
			$momOfTheWeek = MomOfTheWeek::create($data);
			$success = $momOfTheWeek->save();
			// If new user, create
			if(isset($data['password'])){
				// New user, need to register here
			}
			die($success);
		}else{
			// Redirect to the front page
			//$this->redirect('Events::index');
		}
	}
}
?>