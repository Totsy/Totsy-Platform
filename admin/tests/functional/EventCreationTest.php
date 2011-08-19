<?php

namespace admin\tests\functional;

use Testing_Selenium;
use lithium\core\Environment;
use admin\models\User;

class EventCreationTest extends \lithium\test\Integration {

	public $selenium;

	public $user;

	public function testPreperation() {
		$this->user = User::create(array(
			'email' => 'test@example.com',
			'password' => sha1('test'),
			'admin' => true
		));
		$this->user->save();

		$config = Environment::get('test');
		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);

		$this->selenium->start();

		$this->selenium->open('/');
		$this->selenium->type("id=email", 'test@example.com');
		$this->selenium->type("id=password", 'test');
		$this->selenium->click('css=input[type=submit]');
	}

	public function testAdd() {
		$this->selenium->open('/events/add');
		$this->selenium->type("id=Name", uniqid('Test '));
		$this->selenium->click('css=input[type=submit]');

		$result = $this->selenium->isTextPresent($text = 'Editing Event - ');
		$this->assertTrue($result, "Text `{$text}` not present.");
	}

	public function testCleanUp() {
		$this->selenium->stop();
		$this->user->delete();
	}
}

?>