<?php

namespace app\tests\functional;

use li3_fixtures\test\Fixture;
use app\models\Event;
use app\models\Item;
use app\models\User;
use app\models\Cart;
use app\models\Order;
use MongoDate;
use lithium\core\Environment;
use Testing_Selenium;

class CheckoutTest extends \lithium\test\Integration {
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
		$this->fixtures += compact('item');

		$password_plain = 'test_password';
		$password = sha1($password_plain);
		$user = array('firstname' => 'George', 'lastname' => 'Lucas', 'email' => uniqid('george') . '@example.com') + compact('password', 'password_plain');
		$user = User::create($user);
		$user->save(null, array('validate' => false));
		$this->fixtures += compact('user');

		$config = Environment::get(true);
		$this->rootUrl = $config['browserUrl'];

		$this->selenium = new Testing_Selenium($config['browser'], $config['browserUrl']);
		$this->selenium->start();
	}

	public function tearDown() {
		$this->selenium->stop();

		foreach ($this->fixtures as $fixture) {
			$fixture->delete();
		}
	}

	public function testCheckout() {
		$this->selenium->open('/login');

		$email = $this->fixtures['user']->email;
		$password = $this->fixtures['user']->password_plain;
		$this->selenium->type("id=email", $email);
		$this->selenium->type("id=password", $password);

		$this->selenium->click('css=input.button.fr');
		$this->selenium->waitForPageToLoad("30000");

		$url = "/sale/" . $this->fixtures['event']->url . "/" . $this->fixtures['item']->url;
		$this->selenium->open($url);

		$this->selenium->click('id=add-to-cart');
		sleep(10); //we need to wait for the ajax req. to finish

		$expected = 1;
		$this->assertTrue($this->selenium->isTextPresent("My Cart ({$expected})"));

		// 1. step (checkout view ~ cart)
		$this->selenium->open("/checkout/view");

		$this->assertTrue($this->selenium->isTextPresent($this->fixtures['item']->description));

		$this->selenium->click('css=a[href="/checkout/shipping"]');
		$this->selenium->waitForPageToLoad("30000");

		// 2. step (shipping information)
		$expected = $this->rootUrl . "/checkout/shipping";
		$result = $this->selenium->getLocation();
		$this->assertEqual($expected, $result);


		$this->selenium->type("id=firstname", "Testfirstname");
		$this->selenium->type("id=lastname", "Testlastname");
		$this->selenium->type("id=telephone", "123456");
		$this->selenium->type("id=address", "Test test test");
		$this->selenium->type("id=city", "Testcity");
		$this->selenium->select("id=state", "label=Alabama");
		$this->selenium->type("id=zip", "35004");

		$this->selenium->click('css=input.button.fr');
		$this->selenium->waitForPageToLoad("30000");

		// 3. step (payment information)
		$expected = $this->rootUrl . "/checkout/payment";
		$result = $this->selenium->getLocation();
		$this->assertEqual($expected, $result);

		$this->selenium->type("id=card_number", "4111111111111111");
		$this->selenium->select("id=card_month", "label=November");
		$this->selenium->select("id=card_year", "label=2023");
		$this->selenium->type("id=card_code", "123");
		$this->selenium->click("id=opt_shipping");

		$this->selenium->click('css=input.button.fr');
		$this->selenium->waitForPageToLoad("30000");

		// 4.step (checkout review)
		$expected = $this->rootUrl . "/checkout/review";
		$result = $this->selenium->getLocation();
		$this->assertEqual($expected, $result);

		$this->assertTrue($this->selenium->isTextPresent($this->fixtures['item']->description));
		$this->assertTrue($this->selenium->isTextPresent("Qty: 1"));

		//need to remain clickat, click triggers a "do you want to leave the page" ffox popup
		$this->selenium->clickAt("css=div.cart-button.fr a.button", "5,5");
		$this->selenium->waitForPageToLoad("30000");

		// 5. step (order view)
		$user_id = $this->fixtures['user']->_id->{'$id'};
		$order = Order::find('first', array('conditions' => compact('user_id')));

		$result = is_null($order);
		$this->assertFalse($result);

		$expected = $this->fixtures['user']->_id->{'$id'};
		$result = $order->user_id;
		$this->assertEqual($expected, $result);

		$expected = "Testfirstname";
		$result = $order->billing->firstname;
		$this->assertEqual($expected, $result);

		$expected = "Testfirstname";
		$result = $order->shipping->firstname;
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = count($order->items);
		$this->assertEqual($expected, $result);

		$expected = $this->fixtures['item']->_id;
		$result = $order->items[0]->item_id;
		$this->assertEqual($expected, $result);

		$expected = $this->fixtures['event']->_id;
		$result = $order->items[0]->event_id;
		$this->assertEqual($expected, $result);

		$expected = "Order Placed";
		$result = $order->items[0]->status;
		$this->assertEqual($expected, $result);

		$order_id = $order->order_id;

		$expected = $this->rootUrl . "/orders/view/" . $order_id;
		$result = $this->selenium->getLocation();
		$this->assertEqual($expected, $result);

		$this->assertTrue($this->selenium->isTextPresent("Thank you! Your order has been successfully placed!"));
		$this->assertTrue($this->selenium->isTextPresent("Order #{$order_id}"));

		$order->delete();
	}
}

?>