<?php

namespace admin\controllers;
use admin\models\Orders;
use \lithium\storage\Session;
use admin\models\Menu;

class OrdersController extends \lithium\action\Controller {

	public function index(){
		$this->_render['layout'] = 'main';
	}
	public function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$menu = Menu::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));
			$self->set(compact('menu'));
			return $chain->next($self, $params, $chain);
		});
	}
	
}
?>