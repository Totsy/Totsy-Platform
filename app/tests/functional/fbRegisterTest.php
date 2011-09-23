<?php

namespace app\tests\functional;

use Testing_Selenium;
use lithium\core\Environment;

class fbRegisterTest extends \lithium\test\Integration {

	public function fbRegisterTesting() {
		$this->selenium->open('/');
		$this->selenium->type("id=email", 'test@example.com');
		$this->selenium->type("id=password", 'test');
		$this->selenium->click('css=input.button.fr');
		$this->selenium->waitForPageToLoad("30000");
		$this->assertTrue($this->selenium->isTextPresent('Hello, test@example.com (Sign Out)'));
	}
}

?>