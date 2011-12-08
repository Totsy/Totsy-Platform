<?php

namespace app\tests\mocks\controllers;

class OrdersControllerMock extends \app\controllers\OrdersController {

	public $stopped = false;

	public $redirect;

	public function _init() {
		parent::_init();
		$this->_classes['order'] = 'app\tests\mocks\models\OrderMock';
	}

	public function redirect($url, array $options = array()) {
		$this->redirect = func_get_args();
	}

	protected function _stop($status = 0 ) {
		$this->stopped = true;
	}
}

?>