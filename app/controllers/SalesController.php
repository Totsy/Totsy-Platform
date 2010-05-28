<?php

namespace app\controllers;
use app\models\Sales;
use \lithium\storage\Session;


class SalesController extends \lithium\action\Controller {

	public function index(){
		$this->_render['layout'] = 'main';
	}
	
}