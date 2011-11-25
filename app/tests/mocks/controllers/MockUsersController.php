<?php

namespace app\tests\mocks\controllers;

class MockUsersController extends \app\controllers\UsersController {
	public $redirect = array();

	protected function _init() {
		parent::_init();
		$this->_classes['mailer'] = 'app\tests\mocks\extensions\MailerMock';
		$this->resetMailer();
	}

	public function redirect($url, array $options = array()) {
		$this->redirect = func_get_args();
		return 'redirect';
	}

	public function mailer() {
		return $this->_classes['mailer'];
	}

	protected function resetMailer() {
		$mailer = $this->mailer();
		$mailer::reset();
	}
}

?>