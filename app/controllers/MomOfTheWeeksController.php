<?php

namespace app\controllers;

use \app\models\MomOfTheWeek;

class MomOfTheWeeksController extends \lithium\action\Controller {

	public function index() {
		$success = false;
		if($this->request->data){
			$momOfTheWeek = MomOfTheWeek::create($this->request->data);
			$success = $momOfTheWeek->save();
			die($success);
		}else{
			// Redirect to the front page
			//$this->redirect('Events::index');
		}
	}
}
?>