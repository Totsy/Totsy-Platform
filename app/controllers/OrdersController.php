<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Orders;
use lithium\storage\Session;
use app\models\Menu;

class OrdersController extends BaseController {

	public function index(){
		$this->_render['layout'] = 'main';
	}
	
	public function _init() {
		parent::_init();
	}
}

?>