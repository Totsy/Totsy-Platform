<?php

namespace app\controllers;

use app\models\MomOfTheWeek;
use MongoDate;
use app\controllers\UsersController;

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
			if($success == 0){
				return $success;
			}
			// If new user, create
			if(isset($data['password'])){
				// New user, need to register here
				$user['firstname'] = $data['firstname'];
				$user['lastname'] = $data['lastname'];
				$user['email'] = $data['email'];
				$user['confirmemail'] = $data['confirmemail'];
				$user['password'] = $data['password'];
				$user['terms'] = $data['terms'];
				$user['invited_by'] = $data['invited_by'];
				extract(UsersController::registration($user));
				$success = $saved;
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
