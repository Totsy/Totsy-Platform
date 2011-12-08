<?php

namespace admin\tests\functional;

use Testing_Selenium;
use lithium\core\Environment;
use admin\models\User;

class AuthTest extends \lithium\test\Integration {

	public function setUp() {
		$config = Environment::get(true);

		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);
		$this->selenium->start();
	}

	public function tearDown() {
		$this->selenium->stop();
	}

	public function testSignIn() {
		$user = User::create(array(
			'email' => 'test@example.com',
			'password' => sha1('test'),
			'admin' => true
		));
		$user->save();

		$this->selenium->open('/');
		$this->selenium->type("id=email", 'test@example.com');
		$this->selenium->type("id=password", 'test');
		$this->selenium->click('css=input[type=submit]');
		$this->assertTrue($this->selenium->isTextPresent('Totsy Dashboard'));

		$user->delete();
	}
}

?>