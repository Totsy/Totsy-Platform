<?php

namespace app\controllers;
use app\models\User;
use \lithium\storage\Session;
use app\models\Navigation;



class AccountController extends \lithium\action\Controller {
	
	public function index(){
		$data = $this->getUser();
		$this->_render['layout'] = 'main';
		
		return compact("data");
	}
	
	
	public function info() {
		$success = false;
		if ($this->request->data) {			
			//Update database using $set			
			$success = User::update($this->request->data);
			
			//Update the session with correct names	
			Session::write('firstname', $this->request->data['firstname']);
			Session::write('lastname', $this->request->data['lastname']);			
		}
		$data = $this->getUser();
		
		return compact("data", "success");
		
	}
	
	public function getUser() {
		return User::find('first',array('_id' => Session::read('_id')))->data();
	}
	
	public function edit() {
		$status = '';
		$this->_render['layout'] = 'main';
		if($this->request->data){
			
			$status = User::addressUpdate($this->request->data);
		}
		return compact("status");
	}
	
	public function news() {
		$data = "";
		return compact("data");
	}
	
	public function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$navigation = Navigation::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));
			$self->set(compact('navigation'));
			return $chain->next($self, $params, $chain);
		});
	}
	
}
?>