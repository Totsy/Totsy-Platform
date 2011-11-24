<?php

namespace app\tests\cases\models;

use app\models\User;
use app\models\Cart;
use MongoId;
use MongoDate;
use app\models\Item;
use app\models\Event;
use li3_fixtures\test\Fixture;
use lithium\storage\Session;
use app\tests\mocks\storage\session\adapter\MemoryMock;
use lithium\data\collection\DocumentArray;

class CartTest extends \lithium\test\Unit {

	public $user;

	protected $_backup = array();

	protected $_delete = array();

    public function setUp() {
 		$efixture = Fixture::load('Event');
		$ifixture = Fixture::load('Item');
		$cfixture = Fixture::load('Cart');
		$next = $efixture->first();
		do {
			Event::remove(array('_id' => $next['_id'] ));
			foreach($next as $key => $value) {
				if (is_string($value) && preg_match('/_date$/', $key)) {
					$next[$key] = new MongoDate(strtotime($value));
				}
			}
			$event = Event::create();
			$event->save($next);
		} while ($next = $efixture->next());

		$next = $ifixture->first();

		do {
			Item::remove(array('_id' => $next['_id'] ));
			$item = Item::create();
			$item->save($next);
		} while ($next = $ifixture->next());

		$next = $cfixture->first();
		do {
			Cart::remove(array('_id' => $next['_id'] ));
			$cart = Cart::create();
			$cart->save($next);
		} while ($next = $cfixture->next());

		$data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'email' => uniqid('george') . '@example.com',
			'total_credit' => 30
		);
		$this->user = User::create();
		$this->user->save($data, array('validate' => false));
		$this->_delete[] = $this->user;

		Session::config(array(
			'default' => array('adapter' => new MemoryMock())
		));
		$this->user = User::create();
		$this->user->save($data, array('validate' => false));

		$this->_delete[] = $this->user;

		Session::config(array(
			'default' => array('adapter' => new MemoryMock())
		));
	}

	public function tearDown() {
		$efixture = Fixture::load('Event');
		$ifixture = Fixture::load('Item');
		$cfixture = Fixture::load('Cart');

		$event = $efixture->first();
		do {
			Event::remove( array('_id' => $event['_id'] ) );
		} while ($event = $efixture->next());

		$item = $ifixture->first();
		do {
			Item::remove( array( '_id' => $item['_id'] ) );
		} while ($item = $ifixture->next());

		$cart = $cfixture->first();
		do {
			Cart::remove( array('_id' => $cart['_id'] ) );
		} while ($cart = $cfixture->next());

		foreach ($this->_delete as $document) {
			$document->delete();
		}
	}

	public function testDates() {
		$result = Cart::dates('-1min');
		$this->assertTrue(is_a($result, 'MongoDate'));

		$expected = strtotime('-1min');
		$result = $result->sec;
		$this->assertTrue($result == $expected || ($result > $expected && $result < $expected + 5));
	}

	public function testAddFields() {
		Session::write('userLogin', $this->user->data());

		$data = array(
			'description' => 'Fireman Towel',
		);
		$cart = Cart::create($data);
		$cart->save();

		Cart::addFields($cart);

		$cart = Cart::first((string) $cart->_id);

		$expected = strtotime('15min');
		$result = $cart->expires->sec;
		$this->assertTrue($result == $expected || ($result > $expected && $result < $expected + 5));

		$expected = strtotime('now');
		$result = $cart->created->sec;
		$this->assertTrue($result == $expected || ($result > $expected && $result < $expected + 5));

		$result = isset($cart->session);
		$this->assertTrue($result);

		$expected = (string) $this->user->_id;
		$result = $cart->user;
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		$this->_delete[] = $cart;
	}

	public function testActive() {
		Session::write('userLogin', $this->user->data());

		$data = array(
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('-10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$data = array(
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('+10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::active();

		$expected = $cart->_id;
		$this->assertEqual($expected, $result[0]['_id']);

		$expected = $this->user->_id;
		$this->assertEqual($expected, $result[0]['user']);

		$expected = 1;
		$result = count($result);
		$this->assertIdentical($expected, $result);

		Session::delete('userLogin');
	}

	public function testItemCount() {
		Session::write('userLogin', $this->user->data());

		$data = array(
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('+10min')),
			'user' => (string) $this->user->_id,
			'quantity' => 3
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$data = array(
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('+10min')),
			'user' => (string) $this->user->_id,
			'quantity' => 2
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$expected = 5;
		$result = Cart::itemCount();
		$this->assertIdentical($expected, $result);

		Session::delete('userLogin');
	}

	public function testTotalWithShipping() {
		$data = array(
			'taxable' => true
		);
		$item = Item::create($data);
		$item->save();
		$this->_delete[] = $item;

		$data = array(
			'sale_retail' => 3,
			'quantity' => 2,
			'item_id' => (string) $item->_id
		);
		$cart = Cart::create($data);

		$shipping = array(
			'state' => 'CA',
			'zip' => 9999
		);
		$expected = 6;
		$result = $cart->total($shipping);
		$this->assertEqual($expected, $result);
	}

	public function testTotalWithNYShipping() {
		$data = array(
			'taxable' => true
		);
		$item = Item::create($data);
		$item->save();
		$this->_delete[] = $item;

		$data = array(
			'sale_retail' => 3,
			'quantity' => 2,
			'item_id' => (string) $item->_id
		);
		$cart = Cart::create($data);

		$shipping = array(
			'state' => 'NY',
			'zip' => 100
		);
		$expected = (string) 7.065;
		$result = (string) $cart->total($shipping);
		$this->assertEqual($expected, $result);
	}

	public function testSubTotal() {
		$data = array(
			'sale_retail' => 3,
			'quantity' => 2
		);
		$cart = Cart::create($data);

		$expected = 6;
		$result = $cart->subTotal();
		$this->assertEqual($expected, $result);
	}

	public function testTax() {
		$data = array(
			'taxable' => true
		);
		$item = Item::create($data);
		$item->save();
		$this->_delete[] = $item;

		$data = array(
			'sale_retail' => 3,
			'quantity' => 2,
			'item_id' => (string) $item->_id
		);
		$cart = Cart::create($data);

		$shipping = array(
			'state' => 'CA',
			'zip' => 9999
		);
		$expected = 0;
		$result = $cart->tax($shipping);
		$this->assertEqual($expected, $result);

		$shipping = array(
			'state' => 'NY',
			'zip' => 100
		);
		$expected = (string) 0.5325;
		$result = (string) $cart->tax($shipping);
		$this->assertEqual($expected, $result);

		$shipping = array(
			'state' => 'NJ',
			'zip' => 9999
		);
		$expected = (string) 0.42;
		$result = (string) $cart->tax($shipping);
		$this->assertEqual($expected, $result);
	}

	public function testWeight() {
		$data = array(
			'shipping_weight' => '3 kg'
		);
		$item = Item::create($data);
		$item->save();
		$this->_delete[] = $item;

		$data = array(
			'quantity' => 2,
			'item_id' => (string) $item->_id
		);
		$cart = Cart::create($data);

		$expected = 6;
		$result = $cart->weight();
		$this->assertEqual($expected, $result);

		$data = array(
			'shipping_weight' => '3.2 kg'
		);
		$item = Item::create($data);
		$item->save();
		$this->_delete[] = $item;

		$data = array(
			'quantity' => 2,
			'item_id' => (string) $item->_id
		);
		$cart = Cart::create($data);

		$expected = 6;
		$result = $cart->weight();
		$this->assertEqual($expected, $result);

		$data = array(
			'shipping_weight' => '0.2 kg'
		);
		$item = Item::create($data);
		$item->save();
		$this->_delete[] = $item;

		$data = array(
			'quantity' => 2,
			'item_id' => (string) $item->_id
		);
		$cart = Cart::create($data);

		$expected = 2;
		$result = $cart->weight();
		$this->assertEqual($expected, $result);
	}

	public function testWeightWithProductWeight() {
		$data = array(
			'product_weight' => '3 kg'
		);
		$item = Item::create($data);
		$item->save();
		$this->_delete[] = $item;

		$data = array(
			'quantity' => 2,
			'item_id' => (string) $item->_id
		);
		$cart = Cart::create($data);

		$expected = 6;
		$result = $cart->weight();
		$this->assertEqual($expected, $result);
	}

	public function testShippingWithSingleCart() {
		$data = array(
			'shipping_exempt' => false
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			array(
				'item_id' => (string) $item0->_id
			)
		);
		$data = new DocumentArray(compact('data'));

		$expected = 7.95;
		$result = Cart::shipping($data);
		$this->assertEqual($expected, $result);

		$data = array(
			'shipping_exempt' => true
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			array(
				'item_id' => (string) $item0->_id
			)
		);
		$data = new DocumentArray(compact('data'));

		$expected = 0;
		$result = Cart::shipping($data);
		$this->assertEqual($expected, $result);
	}

	public function testShippingWithSingleCartAndOversize() {
		$data = array(
			'shipping_exempt' => false,
			'shipping_oversize' => 2
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			array(
				'item_id' => (string) $item0->_id
			)
		);
		$data = new DocumentArray(compact('data'));

		$expected = 0;
		$result = Cart::shipping($data);
		$this->assertEqual($expected, $result);
	}

	public function testShippingWithMultipleCarts() {
		$data = array(
			'shipping_exempt' => false
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			'shipping_exempt' => true
		);
		$item1 = Item::create($data);
		$item1->save();
		$this->_delete[] = $item1;

		$data = array(
			array(
				'item_id' => (string) $item0->_id
			),
			array(
				'item_id' => (string) $item1->_id
			)
		);
		$data = new DocumentArray(compact('data'));

		$expected = 7.95;
		$result = Cart::shipping($data);
		$this->assertEqual($expected, $result);
	}

	public function testOverSizeShipping() {
		$data = array(
			'shipping_oversize' => 2,
			'shipping_rate' => 3
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			'shipping_rate' => 4
		);
		$item1 = Item::create($data);
		$item1->save();
		$this->_delete[] = $item1;

		$data = array(
			array(
				'item_id' => (string) $item0->_id
			),
			array(
				'item_id' => (string) $item1->_id
			)
		);
		$cart = Cart::create($data);

		$expected = 3;
		$result = Cart::overSizeShipping($cart);
		$this->assertEqual($expected, $result);
	}

	public function testCheckCartItems() {
		$data = array(
			'session' => Session::key('default'),
			'item_id' => 'item0',
			'size' => '18-24M',
			'expires' => new MongoDate(time() + 60)
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::checkCartItem('item0', '18-24M');

		$expected = 0;
		$this->assertEqual(1, count($result));
	}

	public function testReserved() {
		$data = array(
			'item_id' => 'item0',
			'size' => '18-24M',
			'quantity' => 2
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$data = array(
			'item_id' => 'item0',
			'size' => '18-24M',
			'quantity' => 3
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$data = array(
			'item_id' => 'item1',
			'size' => '18-24M',
			'quantity' => 5
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::reserved('item0', '18-24M');

		$expected = 5;
		$this->assertEqual($expected, $result);
	}

	public function testIncreaseExpires() {
		Session::write('userLogin', $this->user->data());

		$data = array(
			'session' => Session::key('default'),
			'expires' => new MongoDate(time() + (4 * 60)),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$data = array(
			'session' => Session::key('default'),
			'expires' => new MongoDate(time() + 10),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::increaseExpires();
		$this->assertTrue($result);

		$left  = $cart;
		$right = Cart::first((string) $left->_id);
		$this->assertNotEqual($left->expires, $right->expires);

		$expected = $left->expires->sec + (60 * 5) - 10;
		$result = $right->expires->sec;
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
	}

	public function testRefreshTimer() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', 999);

		$data = array(
			'end_date' => new MongoDate(strtotime('20min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		$data = array(
			'end_date' => true,
			'event' => array(
				(string) $event->_id
			),
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::refreshTimer();
		$this->assertNull($result);

		$cart = Cart::first((string) $cart->_id);

		$expected = time() + (15 * 60);
		$result = $cart->expires->sec;
		$this->assertEqual($expected, $result);

		$expected = 999;
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testRefreshTimerExcess() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', 999);

		$data = array(
			'end_date' => new MongoDate(strtotime('20min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		for ($i = 0; $i <= 30; $i++) {
			$data = array(
				'end_date' => true,
				'event' => array(
					(string) $event->_id
				),
				'session' => Session::key('default'),
				'expires' => new MongoDate(strtotime('10min')),
				'user' => (string) $this->user->_id
			);
			$cart = Cart::create($data);
			$cart->save();
			$this->_delete[] = $cart;
		}

		Cart::refreshTimer();

		$expected = 0;
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testCleanExpiredEventItems() {
		Session::write('userLogin', $this->user->data());

		$data = array(
			'end_date' => new MongoDate(strtotime('-1min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		$data = array(
			'event' => array(
				(string) $event->_id
			),
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::cleanExpiredEventItems();
		$this->assertNull($result);

		$result = Cart::first((string) $cart->_id);
		$this->assertFalse($result);

		$data = array(
			'end_date' => new MongoDate(strtotime('+1min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		$data = array(
			'event' => array(
				(string) $event->_id
			),
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		Cart::cleanExpiredEventItems();

		$result = Cart::first((string) $cart->_id);
		$this->assertTrue($result);

		Session::delete('userLogin');
	}

	public function testRefreshTimer() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', 999);

		$data = array(
			'end_date' => new MongoDate(strtotime('20min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		$data = array(
			'end_date' => true,
			'event' => array(
				(string) $event->_id
			),
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::refreshTimer();
		$this->assertNull($result);

		$cart = Cart::first((string) $cart->_id);

		$expected = time() + (15 * 60);
		$result = $cart->expires->sec;
		$this->assertEqual($expected, $result);

		$expected = 999;
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testRefreshTimerExcess() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', 999);

		$data = array(
			'end_date' => new MongoDate(strtotime('20min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		for ($i = 0; $i <= 30; $i++) {
			$data = array(
				'end_date' => true,
				'event' => array(
					(string) $event->_id
				),
				'session' => Session::key('default'),
				'expires' => new MongoDate(strtotime('10min')),
				'user' => (string) $this->user->_id
			);
			$cart = Cart::create($data);
			$cart->save();
			$this->_delete[] = $cart;
		}

		Cart::refreshTimer();

		$expected = 0;
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testCleanExpiredEventItems() {
		Session::write('userLogin', $this->user->data());

		$data = array(
			'end_date' => new MongoDate(strtotime('-1min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		$data = array(
			'event' => array(
				(string) $event->_id
			),
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::cleanExpiredEventItems();
		$this->assertNull($result);

		$result = Cart::first((string) $cart->_id);
		$this->assertFalse($result);

		$data = array(
			'end_date' => new MongoDate(strtotime('+1min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		$data = array(
			'event' => array(
				(string) $event->_id
			),
			'session' => Session::key('default'),
			'expires' => new MongoDate(strtotime('10min')),
			'user' => (string) $this->user->_id
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		Cart::cleanExpiredEventItems();

		$result = Cart::first((string) $cart->_id);
		$this->assertTrue($result);

		Session::delete('userLogin');
	}

	/*
	* Testing the Check Method of the Cart
	*/
	public function testCheck() {
		$quantity = 20;
		$cart_id = "787878787zazazag7878";
		$item_id = "87887273782738728";

		$remote = new Cart();
		$datas_cart = array(
			"_id" => $cart_id,
			"category" => "bath&feed",
			"color" => "",
			"created" => Date( "Mon Feb 28 00:48:02 2011" ),
			"description" => "Fireman Towel",
			"discount_exempt" => false,
			"event" =>  array(
							"0" => "4d63ee8c5389267b3c00ce96"
						),
			"expires" => Date( "Mon Feb 28 01:03:02 2011" ),
			"item_id" => $item_id,
			"primary_image" => "4d6b0a185389264b53001140",
			"product_weight" => 1,
			"quantity" => 1,
			"sale_retail" => 19.8,
			"session" => "o274fbl46ra2bggqh42ihcids1",
			"size" => "18-24M",
			"url" => "fireman-towel",
			"user" => "4d6b34965389264a530045b7",
			"vendor_style" => "KIDFIRETOW" );
		$cart = Cart::create();
		$cart->save($datas_cart);
		$datas_item = array(
			"_id" => $item_id,
			"total_quantity" => 112,
			"details" => array(
				"sub_category" => "",
			    "no size" => "",
			    "0-6M" => "",
			    "0-3M" => "",
			    "3-6M" => "",
			    "6-12M" => "",
			    "12-18M" => "",
			    "18-24M" => "27",
			    "2" => "38",
			    "3/4Y" => "13",
			    "5/6Y" => "34" )
		);
		$item = Item::create();
		$item->save($datas_item);

		$result = $remote->check($quantity, $cart_id);

		Cart::remove(array("_id" => $cart_id));
		Item::remove(array("_id" => $item_id));

		$this->assertTrue($result["status"]);
	}

	/* @todo Need more understanding of needed input for getEventIds().
	public function testShipDate() {
		$data = array(
			'end_date' => new MongoDate(strtotime('1min'))
		);
		$event = Event::create($data);
		$event->save();
		$this->_delete[] = $event;

		$data = array(
			'event' => array(
				1 => (string) $event->_id
			)
		);
		$cart = Cart::create($data);
		$cart->save();
		$this->_delete[] = $cart;

		$result = Cart::shipDate($cart);
	}
	*/

	public function testUpdateSavingsAdd() {
		Session::write('userLogin', $this->user->data());

		$data = array(
			'msrp' => 9,
			'sale_retail' => 2
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			'msrp' => 9,
			'sale_retail' => 3
		);
		$item1 = Item::create($data);
		$item1->save();
		$this->_delete[] = $item1;

		$items = array(
			(string) $item0->_id => 2,
			(string) $item1->_id => 3
		);
		$result = Cart::updateSavings($items, 'add');

		$expected = array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testUpdateSavingsUpdate() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));

		$data = array(
			'msrp' => 5,
			'sale_retail' => 2
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			'msrp' => 8,
			'sale_retail' => 1
		);
		$item1 = Item::create($data);
		$item1->save();
		$this->_delete[] = $item1;

		$items = array(
			(string) $item0->_id => 2,
			(string) $item1->_id => 3
		);
		$result = Cart::updateSavings($items, 'update');

		$expected = array(
			'items' => 27,
			'discount' => 0,
			'services' => 0
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testUpdateSavingsRemove() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));

		$data = array(
			'msrp' => 5,
			'sale_retail' => 2
		);
		$item0 = Item::create($data);
		$item0->save();
		$this->_delete[] = $item0;

		$data = array(
			'msrp' => 8,
			'sale_retail' => 1
		);
		$item1 = Item::create($data);
		$item1->save();
		$this->_delete[] = $item1;

		$items = array(
			(string) $item0->_id => 2,
			(string) $item1->_id => 3
		);
		$result = Cart::updateSavings($items, 'remove');

		$expected = array(
			'items' => 5,
			'discount' => 0,
			'services' => 0
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testUpdateSavingsDiscount() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));

		$result = Cart::updateSavings(null, 'discount', 2);

		$expected = array(
			'items' => 32,
			'discount' => 2,
			'services' => 0
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testUpdateSavingsServices() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));

		$result = Cart::updateSavings(null, 'services', 2);

		$expected = array(
			'items' => 32,
			'discount' => 0,
			'services' => 2
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
	}

	public function testGetDiscount() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));
		Session::write('services', array());

		$expected = 0;
		$data = array(
			'code' => false,
			'credit_amount' => 2
		);
		$result = Cart::getDiscount(100, 7.95, 0, $data, 0);

		$this->assertTrue(is_object($result['cartPromo']));
		$this->assertTrue(is_object($result['cartCredit']));
		$this->assertTrue(is_array($result['services']));
		$this->assertTrue(isset($result['postDiscountTotal']));

		$expected = 98;
		$this->assertEqual($expected, $result['postDiscountTotal']);

		Session::delete('userLogin');
		Session::delete('userSavings');
		Session::delete('services');
	}
}

?>