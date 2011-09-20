<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use admin\controllers\OrdersController;
use admin\tests\mocks\models\OrderMock;

class OrdersControllerTest extends \lithium\test\Unit {

	public $controller;

	public function setUp() {
		$this->controller = new OrdersController(array(
			'request' => new Request(),
			'classes' => array(
				'tax' => 'app\tests\mocks\extensions\AvaTaxMock',
				'order' => 'app\tests\mocks\models\OrderMock'
			)
		));
	}
}

?>