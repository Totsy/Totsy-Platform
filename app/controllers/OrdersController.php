<?php

namespace app\controllers;
use app\models\Orders;
use \lithium\storage\Session;
use app\models\Navigation;

class OrdersController extends \lithium\action\Controller {

	public function index(){
		$this->_render['layout'] = 'main';
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