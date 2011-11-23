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
			'email' => uniqid('george') . '@example.com'
		);
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
}

?>