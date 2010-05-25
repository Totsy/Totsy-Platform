<?php

namespace app\controllers;
use app\models\User;
use \lithium\storage\Session;



class AccountController extends \lithium\action\Controller {
	
	public function index(){
		$data = $this->getUser();
		$this->_render['layout'] = 'main';
		
		return compact("data");
	}
	
	
	public function info() {
		$sucess = false;
		if ($this->request->data) {			
			//Update database using $set			
			$sucess = User::update($this->request->data);			
		}
		$data = $this->getUser();
		
		return compact("data", "sucess");
		
	}
	
	public function getUser() {
		return User::find('first',array('_id' => Session::read('_id')))->data();
	}
	
	public function edit() {

		$data = "";
		return compact("data");
	}
	
	public function news() {
		$data = "";
		return compact("data");
	}
}
?>