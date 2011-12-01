<?php

namespace app\tests\functional;

use Testing_Selenium;
use lithium\core\Environment;

class RegistrationTest extends \lithium\test\Integration {
	public function setUp() {
		$config = Environment::get(true);

		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);
		$this->selenium->start();
	}

	public function tearDown() {
		$this->selenium->stop();
	}

	public function testRegister() {
		$this->selenium->open('/logout');

		$this->selenium->open('/register');

		$email = uniqid('user') . '@example.com';
		$this->selenium->type("name=email", $email);
		$this->selenium->type("name=confirmemail", $email);
		$this->selenium->type("name=password", 'test_password');

		$this->selenium->click('css=input.button.fr');
		$this->selenium->waitForPageToLoad("30000");

		$this->assertTrue($this->selenium->isTextPresent("Hello, {$email} (Sign Out)"));
	}
}

?>