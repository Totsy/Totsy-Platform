<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\tests\mocks\controllers\MockUsersController;
use lithium\storage\Session;
use app\models\User;
use li3_fixtures\test\Fixture;

class UsersControllerTest extends \lithium\test\Unit {
	public function setUp() {
		$this->users = Fixture::load('User')->map(function ($fixture) {
			return User::create($fixture);
		});
		$this->saveSession();
	}

	public function tearDown() {
		$this->restoreSession();
	}

	protected function saveSession() {
		$this->sessionSave = Session::read('userLogin');
	}

	protected function restoreSession() {
		Session::write('userLogin', $this->sessionSave);
	}

	protected function load($short) {
		if (is_array($short)) {
			return array_map(array($this, 'load'), $short);
		}
		return $this->users[$short];
	}

	public function testRegistration() {
		$post = array_intersect_key($this->load('user1')->data(), array_fill_keys(array('firstname', 'lastname', 'email', 'confirmemail', 'password', 'terms', 'emailcheck'), null));
		$request = new Request(array(
			'data'=> $post,
			'params' => array('controller' => 'users', 'action' => 'register')
		));
		$testcode = 'testaffiliate';
		$controller = new MockUsersController(compact('request'));
		$result = $controller->register($testcode);
		$this->assertEqual('redirect', $result, $result);
		$this->assertEqual(array('/sales'), $controller->redirect);
		$mailer = $controller->mailer();
		$this->assertEqual(array(array('Welcome_Free_Shipping', $post['email'])), $mailer::$sent);
		$this->assertEqual(array(array($post['email'])), $mailer::$mailing_list);
		$this->assertEqual(array(array($post['email'])), $mailer::$suppression_list);
		$user = Session::read('userLogin');
		if ($result == 'redirect') {
			User::remove(array("_id" => $user['_id']));
		}
	}

	public function testLogin() {
		$user = $this->load('user1');
		$user->password = sha1('testpw');
		$user->save();
		$request = new Request(array(
			'data' => array('email' => $user->email, 'password' => 'testpw', 'remember_me' => false),
			'params' => array('controller' => 'users', 'action' => 'login')
		));
		$controller = new MockUsersController(compact('request'));
		$result = $controller->login();
		$this->assertEqual('redirect', $result, $result);
		$this->assertEqual(array('/sales'), $controller->redirect);
		$user->delete();
	}

	public function testLogout() {
		$request = new Request(array('params' => array('controller' => 'users', 'action' => 'logout')));
		$controller = new MockUsersController(compact('request'));
		$result = $controller->logout();
		$this->assertEqual('redirect', $result, $result);
		$this->assertEqual(array(array('action' => 'login')), $controller->redirect);
	}

	public function testInfo() {
		$request = new Request(array('params' => array('controller' => 'users', 'action' => 'info')));
		$controller = new MockUsersController(compact('request'));
		$result = $controller->info();
		$this->assertTrue(is_array($result), $result);
		$this->assertTrue(isset($result['user']) && isset($result['status']) && isset($result['status']), $result);
		$this->assertTrue($result['user'] !== null);
		$this->assertEqual('default', $result['status']);
		$this->assertEqual(false, $result['connected']);
	}

	public function testReset() {
		$user = $this->load('user1');
		$user->save();
		$request = new Request(array('data' => array('email' => $user->email), 'params' => array('controller' => 'users', 'action' => 'info')));
		$controller = new MockUsersController(compact('request'));
		$result = $controller->reset();
		$this->assertTrue(is_array($result), $result);
		$this->assertTrue(isset($result['message']) && isset($result['success']), $result);
		$this->assertEqual('Your password has been reset. Please check your email.', $result['message']);
		$this->assertEqual(true, $result['success']);
		$mailer = $controller->mailer();
		$this->assertEqual(1, count($mailer::$sent));
		list($subject, $address, $token) = $mailer::$sent[0];
		$updated = User::find($user->_id);
		$this->assertEqual('Reset_Password', $subject);
		$this->assertEqual($user->email, $address);
		$this->assertEqual($updated->clear_token, $token['token']);
		$user->delete();
	}

	public function testResetInvalidEmail() {
		$request = new Request(array('data' => array('email' => 'invalid'), 'params' => array('controller' => 'users', 'action' => 'info')));
		$controller = new MockUsersController(compact('request'));
		$result = $controller->reset();
		$this->assertTrue(is_array($result), $result);
		$this->assertTrue(isset($result['message']) && isset($result['success']), $result);
		$this->assertEqual("This email doesn't exist.", $result['message']);
		$this->assertEqual(false, $result['success']);
	}

	public function testInvite() {
		$request = new Request(array('data' => array('to' => 'test_address', 'message' => 'test message'), 'params' => array('controller' => 'users', 'action' => 'invite')));
		$controller = new MockUsersController(compact('request'));
		$result = $controller->invite();
		$this->assertTrue(is_array($result), $result);
		$this->assertTrue(isset($result['flashMessage']) && isset($result['open']), $result);
		$this->assertEqual('Your invitations have been sent', $result['flashMessage']);
		$mailer = $controller->mailer();
		$this->assertEqual(1, count($mailer::$sent));
		list($subject, $address,) = $mailer::$sent[0];
		$this->assertEqual('Friend_Invite', $subject);
		$this->assertEqual('test_address', $address);
		$this->assertEqual(1, $result['open']->count());
		$result['open'][0]->delete();
	}

	protected function checkPassword($block, array $options = array()) {
		$options += array('user' => 'user1', 'password' => null, 'new_password' => 'new_test_pass', 'new_password_confirm' => null);
		$user = $this->load($options['user']);
		$plain_password = $user->password;
		$user->password = sha1($plain_password);
		$user->save();
		Session::write('userLogin', $user);
		$request = new Request(array(
			'data' => array(
				'password' => ($options['password'] ?: $plain_password), 'new_password' => $options['new_password'],
				'password_confirm' => ($options['new_password_confirm'] ?: $options['new_password'])
			),
			'params' => array('controller' => 'users', 'action' => 'invite'))
		);
		$controller = new MockUsersController(compact('request'));
		$result = $controller->password();
		$self = $this;
		$block(compact('user', 'request', 'controller', 'result', 'self'));
		$user->delete();
	}

	public function testPassword() {
		$this->checkPassword(function ($params) {
			extract($params);
			$self->assertTrue(is_array($result), $result);
			$self->assertTrue(isset($result['status']), $result);
			$self->assertEqual('true', $result['status']);
			$updated = User::find($user->_id);
			$self->assertEqual(sha1('new_test_pass'), $updated->password);
		});
	}

	public function testPasswordWrong() {
		$this->checkPassword(function ($params) {
			extract($params);
			$self->assertTrue(is_array($result), $result);
			$self->assertTrue(isset($result['status']), $result);
			$self->assertEqual('false', $result['status']);
		}, array('password' => 'invalid'));
	}

	public function testPasswordMismatch() {
		$this->checkPassword(function ($params) {
			extract($params);
			$self->assertTrue(is_array($result), $result);
			$self->assertTrue(isset($result['status']), $result);
			$self->assertEqual('errornewpass', $result['status']);
		}, array('new_password_confirm' => 'invalid'));
	}

	public function testPasswordTooShort() {
		$this->checkPassword(function ($params) {
			extract($params);
			$self->assertTrue(is_array($result), $result);
			$self->assertTrue(isset($result['status']), $result);
			$self->assertEqual('shortpass', $result['status']);
		}, array('new_password' => 'short'));
	}
}

?>