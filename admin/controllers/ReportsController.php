<?php

namespace admin\controllers;
use admin\models\Cart;



class ReportsController extends \lithium\action\Controller {


	public function index() {

	}

	public function cart() {
		$this->_render['layout'] = false;
		$y = Cart::count();
		$x = time() * 1000;
		echo "[$x, $y]";
	}
	

}

?>