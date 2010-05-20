<?php

namespace app\controllers;
use app\models\User;
use \lithium\storage\Session;



class AccountController extends \lithium\action\Controller {
	
	public function index(){
		
	}
	
	
	public function info() {
		$sucess = false;
		if ($this->request->data) {			
			//Update database using $set			
			$sucess = User::update($this->request->data);			
		}
		
		$user = User::find('first',array('_id' => Session::read('_id')));
		$data = $user->data();
		
	
		return compact("data", "sucess");
		
	}
}
?>