<?php

namespace app\tests\functional;

use Testing_Selenium;
use lithium\core\Environment;

class AuthTest extends \lithium\test\Integration {

	public function setUp() {
		$config = Environment::get(true);

		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);
		$this->selenium->start();
		$this->selenium->setSpeed(1);
	}

	public function tearDown() {
		$this->selenium->stop();
	}

	public function testSignIn() {
		$this->selenium->open('/');
		$this->selenium->type("id=email", 'test@example.com');
		$this->selenium->type("id=password", 'test');
		$this->selenium->click('css=input.button.fr');
		$this->selenium->waitForPageToLoad("30000");
		$this->assertTrue($this->selenium->isTextPresent('Hello, test@example.com (Sign Out)'));
	}
}

?>