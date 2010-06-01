<?php

namespace app\controllers;
use app\models\Sales;
use \lithium\storage\Session;


class SalesController extends \lithium\action\Controller {

	public function index(){
		$this->_render['layout'] = 'main';
	}
	
	public function add() {
		$status = '';
		$this->_render['layout'] = 'main';
		
		$addressId = func_get_args();
		
		//@todo - We may need to build in some verification of the id
		
		if($addressId){
			//Set fields to return
			$fields = array('info' => "$addressId");
			$data = $this->getUser($fields);
			var_dump($data['Addresses']);
		}
		if($this->request->data){
			
			$status = User::addressUpdate($this->request->data);
		}
		return compact("status");
	}
	
}