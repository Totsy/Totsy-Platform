<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\tests\mocks\controllers\MockUsersController;
use lithium\storage\Session;
use app\models\User;
use li3_fixtures\test\Fixture;

class UsersControllerTest extends \lithium\test\Unit {
	public function setUp() {
		$this->sessionConfig = Session::Config();
		Session::config(array(
			'default' => array('adapter' => 'app\tests\mocks\storage\session\adapter\MemoryMock'),
			'cookie' => array('adapter' => 'app\tests\mocks\storage\session\adapter\MemoryMock')
		));

		$this->users = Fixture::load('User')->map(function ($fixture) {
			/* Ensure we're always able to insert this record. */
			$fixture['confirmemail'] = $fixture['email'] = uniqid('user') . '@example.com';
			return User::create($fixture);
		});
	}

	public function tearDown() {
		Session::Config($this->sessionConfig);
	}

	public function testRegistration() {
		$original_data = $this->users['user1']->data();
		$keep_keys = array('firstname', 'lastname', 'email', 'confirmemail', 'password', 'terms', 'emailcheck');
		$post = array_intersect_key($original_data, array_fill_keys($keep_keys, null));

		/* Ensure we're always able to insert this record. */
		$post['confirmemail'] = $post['email'] = uniqid('user1') . '@example.com';

		$request = new Request(array(
			'data'=> $post,
			'params' => array('controller' => 'users', 'action' => 'register')
		));
		$testcode = 'testaffiliate';
		$controller = new MockUsersController(compact('request'));
		$return = $controller->register($testcode);
		$mailer = $controller->mailer();

		$expected = 'redirect';
		$result = $return;
		$this->assertEqual($expected, $result);

		$expected = array('/sales');
		$result = $controller->redirect;
		$this->assertEqual($expected, $result);

		$expected = array(array('Welcome_Free_Shipping', $post['email']));
		$result = $mailer::$sent;
		$this->assertEqual($expected, $result);

		$expected = array(array($post['email']));
		$result = $mailer::$mailing_list;
		$this->assertEqual($expected, $result);

		$expected = array(array($post['email']));
		$result = $mailer::$suppression_list;
		$this->assertEqual($expected, $result);

		$user = Session::read('userLogin');
		if (isset($user['_id'])) {
			User::remove(array("_id" => $user['_id']));
		}
	}

	public function testLogin() {
		$user = $this->users['user1'];
		$user->password = sha1('testpw');
		$user->save(null, array('validate' => false));
		$request = new Request(array(
			'data' => array('email' => $user->email, 'password' => 'testpw', 'remember_me' => false),
			'params' => array('controller' => 'users', 'action' => 'login'), 'url' => 'test_url'
		));
		$controller = new MockUsersController(compact('request'));
		$return = $controller->login();

		$expected = 'redirect';
		$result = $return;
		$this->assertEqual($expected, $result);

		$expected = array('test_url');
		$result = $controller->redirect;
		$this->assertEqual($expected, $result);

		$user->delete();
	}

	public function testLogout() {
		$request = new Request(array('params' => array('controller' => 'users', 'action' => 'logout')));
		$controller = new MockUsersController(compact('request'));
		$return = $controller->logout();

		$expected = 'redirect';
		$result = $return;
		$this->assertEqual($expected, $result);

		$expected = array(array('action' => 'login'));
		$result = $controller->redirect;
		$this->assertEqual($expected, $result);
	}

	public function testInfo() {
		$user = $this->users['user1'];
		$user->save(null, array('validate' => false));
		Session::write('userLogin', $user->data());

		$request = new Request(array('params' => array('controller' => 'users', 'action' => 'info')));
		$controller = new MockUsersController(compact('request'));
		$return = $controller->info();

		$result = is_array($return);
		$message = $return;
		$this->assertTrue($result, $message);

		$result = isset($return['user']) && isset($return['status']) && isset($return['status']);
		$message = $return;
		$this->assertTrue($result, $message);

		$result = is_null($return['user']);
		$this->assertFalse($result);

		$expected = 'default';
		$result = $return['status'];
		$this->assertEqual($expected, $result);

		$result = $return['connected'];
		$this->assertFalse($result);

		$user->delete();
	}

	public function testReset() {
		$user = $this->users['user1'];
		$user->save(null, array('validate' => false));
		$request = new Request(array('data' => array('email' => $user->email), 'params' => array('controller' => 'users', 'action' => 'info')));
		$controller = new MockUsersController(compact('request'));
		$return = $controller->reset();
		$mailer = $controller->mailer();
		list($subject, $address, $token) = isset($mailer::$sent[0]) ? $mailer::$sent[0] : array(null, null, null);

		$updated = User::find((string) $user->_id);

		$result = is_array($return);
		$message = $return;
		$this->assertTrue($result, $message);

		$result = isset($return['message']) && isset($return['success']);
		$message = $return;
		$this->assertTrue($result, $message);

		$expected = 'Your password has been reset. Please check your email.';
		$result = $return['message'];
		$this->assertEqual($expected, $result);

		$result = $return['success'];
		$this->assertTrue($result);

		$expected = 1;
		$result = count($mailer::$sent);
		$this->assertEqual($expected, $result);

		$expected = 'Reset_Password';
		$result = $subject;
		$this->assertEqual($expected, $result);

		$expected = $user->email;
		$result = $address;
		$this->assertEqual($expected, $result);

		$expected = $updated->clear_token;
		$result = $token['token'];
		$this->assertEqual($expected, $result);

		$user->delete();
	}

	public function testResetInvalidEmail() {
		$request = new Request(array('data' => array('email' => 'invalid'), 'params' => array('controller' => 'users', 'action' => 'info')));
		$controller = new MockUsersController(compact('request'));
		$return = $controller->reset();

		$result = is_array($return);
		$message = $return;
		$this->assertTrue($result, $message);

		$result = isset($return['message']) && isset($return['success']);
		$message = $return;
		$this->assertTrue($result, $message);

		$expected = "This email doesn't exist.";
		$result = $return['message'];
		$this->assertEqual($expected, $result);

		$result = $return['success'];
		$this->assertFalse($result);
	}

	public function testInvite() {
		$user = $this->users['user1'];
		$user->save(null, array('validate' => false));
		Session::write('userLogin', $user->data());

		$request = new Request(array('data' => array('to' => 'test_address', 'message' => 'test message'), 'params' => array('controller' => 'users', 'action' => 'invite')));
		$controller = new MockUsersController(compact('request'));
		$return = $controller->invite();
		$mailer = $controller->mailer();
		list($subject, $address,) = $mailer::$sent[0];

		$result = is_array($return);
		$message = $return;
		$this->assertTrue($result, $message);

		$result = isset($return['flashMessage']) && isset($return['open']);
		$message = $return;
		$this->assertTrue($result, $message);

		$expected = 'Your invitations have been sent';
		$result = $return['flashMessage'];
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = count($mailer::$sent);
		$this->assertEqual($expected, $result);

		$expected = 'Friend_Invite';
		$result = $subject;
		$this->assertEqual($expected, $result);

		$expected = 'test_address';
		$result = $address;
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = $return['open']->count();
		$this->assertEqual($expected, $result);

		if (isset($return['open'][0])) {
			$return['open'][0]->delete();
		}
	}

	protected function _checkPassword($block, array $options = array()) {
		$options += array('user' => 'user1', 'password' => null, 'new_password' => 'new_test_pass', 'new_password_confirm' => null);
		$user = $this->users[$options['user']];
		$plain_password = $user->password;
		$user->password = sha1($plain_password);
		$user->save(null, array('validate' => false));
		Session::write('userLogin', $user);
		$request = new Request(array(
			'data' => array(
				'password' => ($options['password'] ?: $plain_password), 'new_password' => $options['new_password'],
				'password_confirm' => ($options['new_password_confirm'] ?: $options['new_password'])
			),
			'params' => array('controller' => 'users', 'action' => 'invite'))
		);
		$controller = new MockUsersController(compact('request'));
		$return = $controller->password();
		$self = $this;
		$block(compact('user', 'request', 'controller', 'return', 'self'));
		$user->delete();
	}

	public function testPassword() {
		$this->_checkPassword(function ($params) {
			extract($params);
			$updated = User::find((string) $user->_id);

			$result = is_array($return);
			$message = "testPassword() on line " . __LINE__;
			$self->assertTrue($result, $message);

			$result = isset($return['status']);
			$message = "testPassword() on line " . __LINE__;
			$self->assertTrue($result, $message);

			$expected = 'true';
			$result = $return['status'];
			$message = "testPassword() on line " . __LINE__;
			$self->assertEqual($expected, $result, $message);

			$expected = sha1('new_test_pass');
			$result = $updated->password;
			$message = "testPassword() on line " . __LINE__;
			$self->assertEqual($expected, $result, $message);
		});
	}

	public function testPasswordWrong() {
		$this->_checkPassword(function ($params) {
			extract($params);

			$result = is_array($return);
			$message = $return;
			$self->assertTrue($result, $message);

			$result = isset($return['status']);
			$message = $return;
			$self->assertTrue($result, $message);

			$expected = 'false';
			$result = $return['status'];
			$self->assertEqual($expected, $result);
		}, array('password' => 'invalid'));
	}

	public function testPasswordMismatch() {
		$this->_checkPassword(function ($params) {
			extract($params);

			$result = is_array($return);
			$message = $return;
			$self->assertTrue($result, $message);

			$result = isset($return['status']);
			$message = $return;
			$self->assertTrue($result, $message);

			$expected = 'errornewpass';
			$result = $return['status'];
			$self->assertEqual($expected, $result);
		}, array('new_password_confirm' => 'invalid'));
	}

	public function testPasswordTooShort() {
		$this->_checkPassword(function ($params) {
			extract($params);

			$result = is_array($return);
			$message = $return;
			$self->assertTrue($result, $message);

			$result = isset($return['status']);
			$message = $return;
			$self->assertTrue($result, $message);

			$expected = 'shortpass';
			$result = $return['status'];
			$self->assertEqual($expected, $result);
		}, array('new_password' => 'short'));
	}
}

?>