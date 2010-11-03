<?php

namespace app\controllers;

use \app\models\MomOfTheWeek;
use \MongoDate;
use \app\controllers\UsersController;

class MomOfTheWeeksController extends \lithium\action\Controller {

	public function index() {
		$success = false;
		if($this->request->data){
			// Create the sweepstakes entry
			$data = $this->request->data;
			$data['date_created'] = new MongoDate();
			$data['invited_by'] = 'momoftheweek';
			$success = MomOfTheWeek::collection()->update(
				array( 'email' => $data['email'] ),
				$data,
				array( 'upsert' => true )
			);
			// If new user, create
			if(isset($data['password'])){
				// New user, need to register here
				unset($data['answer_1'], $data['answer_2'], $data['answer_3'], $data['answer_4'], $data['answer_5'], $data['answer_6']);
				$success = UsersController::registration($data);
			}
			die($success);
		}else{
			// Redirect to the front page
			//$this->redirect('Events::index');
		}
	}
	
	public function fbml() {
		$this->render(array('layout' => false));
//		MomOfTheWeek::collection()->update( array $criteria , array $newobj [, array $options = array() ] )
	}
}
?>
