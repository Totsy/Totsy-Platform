<?php

namespace app\tests\mocks\controllers;

class MockUsersController extends \app\controllers\UsersController {

	public $redirect = array();

	public function redirect($url, array $options = array()) {
		$this->redirect = func_get_args();
	}
}

?>