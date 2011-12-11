<?php

namespace app\tests\functional;

use li3_fixtures\test\Fixture;
use app\models\Event;
use app\models\Item;
use app\models\User;
use app\models\Cart;
use MongoDate;
use lithium\core\Environment;
use Testing_Selenium;

class AddToCartTest extends \lithium\test\Integration {
	public function setUp() {
		$this->fixtures = array();

		$events = Fixture::load('Event');
		$event = $events['event2'];
		Event::remove(array('_id' => $event['_id']));
		foreach ($event as $field => $value) {
			if (is_string($value) && preg_match('/_date$/', $field)) {
				$event[$field] = new MongoDate(strtotime($value));
			}
		}
		$event['enabled'] = true;
		$event = Event::create($event);
		$event->save(null, array('validate' => false));
		$this->fixtures += compact('event');

		$items = Fixture::load('Item');
		$item = $items['item3'];
		Item::remove(array('_id' => $item['_id']));
		$item['enabled'] = true;
		$item = Item::create($item);
		$item->save(null, array('validate' => false));
		// remove all orders for this item
		Cart::remove(array('item_id' => $item->_id));
		$this->fixtures += compact('item');

		$password_plain = 'test_password';
		$password = sha1($password_plain);
		$user = array('email' => uniqid('george') . '@example.com') + compact('password', 'password_plain');
		$user = User::create($user);
		$user->save(null, array('validate' => false));
		$this->fixtures += compact('user');

		$config = Environment::get(true);

		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);
		$this->selenium->start();
	}

	public function tearDown() {
		$this->selenium->stop();

		foreach ($this->fixtures as $fixture) {
			$fixture->delete();
		}
	}

	public function testAddToCart() {
		$this->selenium->open('/login');

		$email = $this->fixtures['user']->email;
		$password = $this->fixtures['user']->password_plain;
		$this->selenium->type("id=email", $email);
		$this->selenium->type("id=password", $password);

		$this->selenium->click('css=input.button.fr');
		$this->selenium->waitForPageToLoad("30000");

		$this->assertTrue($this->selenium->isTextPresent("Hello, {$email} (Sign Out)"));

		$url = "/sale/" . $this->fixtures['event']->url . "/" . $this->fixtures['item']->url;
		$this->selenium->open($url);

		$expected = 0;
		$this->assertTrue($this->selenium->isTextPresent("My Cart ({$expected})"));

		$this->selenium->click('id=add-to-cart');
		sleep(10); //we need to wait for the ajax req. to finish

		$expected = 1;
		$this->assertTrue($this->selenium->isTextPresent("My Cart ({$expected})"));

		Cart::remove(array('user' => $this->fixtures['user']->_id->{'$id'}));
	}
}

?>