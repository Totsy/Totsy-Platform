<?php

namespace admin\tests\functional;

use Testing_Selenium;
use lithium\core\Environment;
use admin\models\User;

class EventCreationTest extends \lithium\test\Integration {

	public $selenium;

	public $user;

	public function __construct(array $config = array()) {
		parent::__construct($config);

		$config = Environment::get(true);
		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);
		$this->selenium->start();

		$this->user = User::create(array(
			'email' => 'test@example.com',
			'password' => sha1('test'),
			'admin' => true
		));
		$this->user->save();
	}

	public function __destruct() {
		$this->selenium->stop();
		$this->user->delete();
	}

	public function testPreperationSignIn() {
		$this->selenium->open('/');
		$this->selenium->type("id=email", 'test@example.com');
		$this->selenium->type("id=password", 'test');
		$this->selenium->click('css=input[type=submit]');
	}

	public function testAdd() {
		$this->selenium->open('/events/add');
		$this->selenium->type("id=Name", uniqid('Test '));
		$this->selenium->click('css=input[type=submit]');

		$result = $this->selenium->verifyTextPresent($text = 'Editing Event - ');
		$this->assertTrue($result, "Text `{$text}` not present.");
	}
}

?>