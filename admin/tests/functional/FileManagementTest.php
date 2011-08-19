<?php

namespace admin\tests\functional;

use Testing_Selenium;
use lithium\core\Environment;
use admin\models\User;
use admin\models\File;

class FileManagementTest extends \lithium\test\Integration {

	public $selenium;

	public $user;

	protected $_backup = array();

	public function testPreperation() {
		$config = Environment::get('test');
		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);

		$this->user = User::create(array(
			'email' => 'test@example.com',
			'password' => sha1('test'),
			'admin' => true
		));

		$this->user->save();
		$this->selenium->start();

		$this->selenium->open('/');
		$this->selenium->type("id=email", 'test@example.com');
		$this->selenium->type("id=password", 'test');
		$this->selenium->click('css=input[type=submit]');
	}

	public function testPendingItemsAppearAndCanBeDeleted() {
		$this->_backup['dedupe'] = File::$dedupe;
		File::$dedupe = false;

		$file = LITHIUM_APP_PATH . '/tests/data/image_jpg.jpg';
		$bytes = file_get_contents($file);
		$file = File::write($bytes, array('name' => 'test-image.jpg', 'pending' => true));

		$this->selenium->open('/files');

		$result = $this->selenium->isTextPresent($text = 'test-image.jpg');
		$this->assertTrue($result, "Text `{$text}` not present.");

		$element = "//a[contains(@href, '/files/delete/{$file->_id}')]";
		$result = $this->selenium->isElementPresent($element);
		$this->assertTrue($result, "Element `{$element}` not present.");

		$this->selenium->click($element);
		sleep(2);

		$element = "//a[contains(@href, '/files/delete/{$file->_id}')]";
		$result = $this->selenium->isElementPresent($element);
		$this->assertFalse($result, "Element `{$element}` present.");

		$file->delete();
		File::$dedupe = $this->_backup['dedupe'];
	}

	public function testCleanUp() {
		$this->selenium->stop();
		$this->user->delete();
	}
}

?>