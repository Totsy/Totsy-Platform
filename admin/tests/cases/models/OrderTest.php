<?php

namespace admin\tests\cases\models;

use admin\models\Order;
use admin\tests\mocks\models\OrderMock;
use admin\tests\mocks\extensions\PaymentsMock;
use admin\models\User;
use admin\models\Item;
use MongoId;
use MongoDate;

class OrderTest extends \lithium\test\Unit {

	public $user;

	protected $_backup = array();

	protected $_delete = array();

	public function setUp() {
		$data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'email' => uniqid('george') . '@example.com'
		);
		$this->user = User::create();
		$this->user->save($data, array('validate' => false));

		$this->_delete[] = $this->user;
	}

	public function tearDown() {
		foreach ($this->_delete as $document) {
			$document->delete();
		}
		PaymentsMock::resetMock();
	}

	public function testDates() {
		$result = OrderMock::dates('now');
		$this->assertTrue(is_a($result, 'MongoDate'));
	}

	/* @fixme Fails as method isn't working with Object IDs. */
	/*
	public function testLookup() {
		$order = OrderMock::create();
		$result = $order->save(array('title' => 'test'), array('validate' => false));
		$this->assertTrue($result);

		$order->order_id = (string) $order->_id;
		$result = $order->save(array('order_id' => $order->_id), array('validate' => false));
		$this->assertTrue($result);

		$result = OrderMock::lookup(substr((string) $order->_id, 0));
		$this->assertTrue($result);

		if ($result) {
			$expected = (string) $order->_id;
			$result = (string) $result->_id;
			$this->assertEqual($expected, $result);
		}

		$result = OrderMock::lookup(strtoupper((string) $order->_id));
		$this->assertTrue($result);

		if ($result) {
			$expected = (string) $order->_id;
			$result = (string) $result->_id;
			$this->assertEqual($expected, $result);
		}

		$order->delete();
	}
	*/

	public function testVoidWithTotalPositive() {
		$data = array(
			'total' => 1.23,
			'authKey' => '090909099909'
		);
		$order = OrderMock::create($data);
		$order->save();

		$result = OrderMock::void($order->data());
		$this->assertTrue($result);

		$order = OrderMock::first((string) $order->_id);

		$expected = '090909099909';
		$result = $order->authKey;
		$this->assertEqual($expected, $result);

		$result = $order->auth_error;
		$this->assertFalse($result);

		$expected = 'transaction id';
		$result = $order->void_confirm;
		$this->assertEqual($expected, $result);

		$result = $order->void_date;
		$this->assertTrue($result);

		$expected = '090909099909';
		$result = PaymentsMock::$void[1];
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testVoidFailingWithTotalZero() {
		$data = array(
			'total' => 0,
			'authKey' => '090909099909'
		);
		$order = OrderMock::create($data);
		$order->save();

		$result = OrderMock::void($order->data());
		$this->assertTrue($result);

		$order = OrderMock::first((string) $order->_id);

		$expected = '090909099909';
		$result = $order->authKey;
		$this->assertEqual($expected, $result);

		$expected = "Can't capture because total is zero.";
		$result = $order->auth_error;
		$this->assertEqual($expected, $result);

		$result = $order->void_confirm;
		$expected = -1;
		$this->assertEqual($expected, $result);

		$result = $order->void_date;
		$this->assertTrue($result);

		$result = PaymentsMock::$void;
		$this->assertFalse($result);

		$order->delete();
	}

	public function testProcess() {
		$data = array(
			'total' => 1.23,
			'authKey' => '090909099909'
		);
		$order = OrderMock::create($data);
		$result = $order->save();
		$this->assertTrue($result);

		$result = OrderMock::process($order);
		$this->assertTrue($result);

		$order = OrderMock::first(array(
			'conditions' => array('_id' => $order->_id)
		));

		$result = $order->payment_date;
		$this->assertTrue($result);

		$result = $order->auth_error;
		$this->assertFalse($result);

		$expected = 'transaction id';
		$result = $order->auth_confirmation;
		$this->assertEqual($expected, $result);

		$expected = '090909099909';
		$result = PaymentsMock::$capture[1];
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testProcessFailingWithTotalZero() {
		$data = array(
			'total' => 0,
			'authKey' => '090909099909'
		);
		$order = OrderMock::create($data);
		$order->save();

		$result = OrderMock::process($order->data());
		$this->assertTrue($result);

		$order = OrderMock::first((string) $order->_id);

		$expected = '090909099909';
		$result = $order->authKey;
		$this->assertEqual($expected, $result);

		$expected = "Can't capture because total is zero.";
		$result = $order->auth_error;
		$this->assertEqual($expected, $result);

		$result = $order->auth_confirmation;
		$expected = -1;
		$this->assertEqual($expected, $result);

		$result = PaymentsMock::$capture;
		$this->assertFalse($result);

		$order->delete();
	}

	public function testSetTrackingNumber() {
		$order = OrderMock::create(array('tracking_numbers' => array()));
		$order->save(null, array('validate' => false));
		$order->save(array('order_id' => $order->_id), array('validate' => false));

		OrderMock::setTrackingNumber($order->_id, 'test');
		$order = OrderMock::first((string) $order->_id);

		$expected = array('test');
		$result = $order->tracking_numbers->data();
		$this->assertEqual($expected, $result);

		$order->delete();

		$order = OrderMock::create(array('tracking_numbers' => array('test a')));
		$order->save(null, array('validate' => false));
		$order->save(array('order_id' => $order->_id), array('validate' => false));

		OrderMock::setTrackingNumber($order->_id, 'test b');
		$order = OrderMock::first((string) $order->_id);

		$expected = array('test a', 'test b');
		$result = $order->tracking_numbers->data();
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testOrderSearchByName() {
		$data = array(
			'_test' => 'a',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'shipping' => array(
				'firstname' => 'George',
				'lastname' => 'Opossum'
			)
		);
		$order1 = Order::create($data);
		$order1->save(null, array('validate' => false));

		$data = array(
			'_test' => 'b',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'billing' => array(
				'firstname' => 'Leonardo',
				'lastname' => 'di Caprio'
			)
		);
		$order2 = Order::create($data);
		$order2->save(null, array('validate' => false));

		$expected = 'a';
		$result = current(iterator_to_array(Order::orderSearch('George', 'name')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'a';
		$result = current(iterator_to_array(Order::orderSearch('Opossum', 'name')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'b';
		$result = current(iterator_to_array(Order::orderSearch('Leonardo', 'name')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'b';
		$result = current(iterator_to_array(Order::orderSearch('di Caprio', 'name')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'b';
		$result = current(iterator_to_array(Order::orderSearch('Leo', 'name')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'b';
		$result = current(iterator_to_array(Order::orderSearch('Caprio', 'name')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'b';
		$result = current(iterator_to_array(Order::orderSearch('CAPRIO', 'name')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$order1->delete();
		$order2->delete();
	}

	public function testOrderSearchByAdress() {
		$data = array(
			'_test' => 'a',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'shipping' => array(
				'address' => 'Venice Rd'
			)
		);
		$order1 = Order::create($data);
		$order1->save(null, array('validate' => false));

		$data = array(
			'_test' => 'b',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'billing' => array(
				'address' => 'Cloud Blvd'
			)
		);
		$order2 = Order::create($data);
		$order2->save(null, array('validate' => false));

		$expected = 'a';
		$result = current(iterator_to_array(Order::orderSearch('Venice Rd', 'address')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'b';
		$result = current(iterator_to_array(Order::orderSearch('Cloud', 'address')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$expected = 'b';
		$result = current(iterator_to_array(Order::orderSearch('cLouD', 'address')));
		$result = $result['_test'];
		$this->assertEqual($expected, $result);

		$order1->delete();
		$order2->delete();
	}

	public function testCancel() {
		//Configuration Test
		$author = "test";
		$comment = "commment @test !";
		$user_id = "787878787zazazag78dsdsdsds78";
		$order_id = "8788727dsds3782738dsdsds728";
		//Create temporary documents
		$remote = new OrderMock();
		$order_datas = array(
			"_id" => new MongoId($order_id),
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => "4d038da6ce64e5973e8a1500",
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => "4cffa43ace64e5ae3e181900",
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 3,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => $user_id
		);
		$user_datas = array(
			"_id" => new MongoId($user_id),
			"active" => 1,
			"created_on" => "Wed, 22 Sep 2010 16: 50: 44 -0400",
			"email" => "fdkflkdlskfd@gmail.com",
			"firstname" => "KLKL",
			"invitation_codes" => array(
			"0" => "fdfdfdddd"
			),
			"invited_by" => "fdfdfd",
			"lastip" => "204.246.230.160",
			"lastlogin" => "Thu, 10 Mar 2011 22: 42: 08 -0500",
			"lastname" => "OPOo",
			"legacy" => 0,
			"logincounter" => 9,
			"password" => "0b505f152dc80b527035e3500925936fe9703d2c",
			"purchase_count" => 2,
			"reset_token" => "0",
			"total_credit" => 0
		);
		$order = OrderMock::create();
		$order->save($order_datas);
		$user = User::create();
		$user->save($user_datas);
		//Request the tested method
		$remote->cancel((string)$order["_id"], $author, $comment);
		//Check Datas Order
		$check = true;
		$result_order = OrderMock::find('first', array('conditions' => array(
			'_id' => $order["_id"]
		)));
		$order = $result_order->data();
		if($order["cancel"] != true) {
			$check = false;
		}
		foreach($order["items"] as $item) {
			if($item["cancel"] != true){
				$check = false;
			}
		}
		//Check Datas User
		$result_user = User::find('first', array('conditions' => array(
			'_id' => $user["_id"]
		)));

		$this->assertTrue($result_user);
		$this->skipIf(!is_object($result_user), "Can't continue result is not an object.");

		$user = $result_user->data();
		$check_user = false;
		foreach($order["modifications"] as $modif)
		{
			if($modif["comment"] == $comment) {
				$check_user = true;
			}
		}
		if($check_user == false) {
			$check = false;
		}
		//Delete Temporary Documents
		OrderMock::remove(array("_id" => $order_id));
		User::remove(array("_id" => $user_id));
		//Test result
		$this->assertEqual( true , $check);
	}

	public function testShipping() {
		$item_id = new MongoId('4ddsqsdqszzz80f3ad53892614080076e0');
		$remote = new OrderMock();
		$items = array(
			"0" => array(
				"_id" => new MongoId("4ddsqsdqszzz80f3ad53892614080076e0"),
				"category" => "Room D\u00e9cor",
				"color" => "",
				"description" => "test",
				"discount_exempt" => true,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string) $item_id,
				"primary_image" => "4d015488ce64e5c072fc1e00",
				"product_weight" => 0.64,
				"quantity" => 5,
				"sale_retail" => 3,
				"size" => "no size",
				"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
				"event_name" => "Babyganics",
				"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed"
		));
		$item_datas = array(
		  "_id" => $item_id,
		  "category" => "Room D\u00e9cor",
		  "color" => "",
		  "created_date" => "Wed, 16 Mar 2011 13:30:21 -0400",
		  "description" => "test",
		  "details" => array (
		    "no size" => 3
		  ),
		  "discount_exempt" => true,
		  "enabled" => true,
		  "event" => array(
		    "4cfdfdfdfdfd1dd1ce64e5300aeb4100"
		  ),
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_details" => array(
		    "no size" => array(
		      "sale_count" => 1
		    )
		  ),
		  "sale_retail" => 168,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "total_quantity" => 3,
		  "url" => "url_test",
		  "vendor" => "fdeeee",
		  "vendor_style" => "SDDSER.SO16",
		  "views" => 3
		);
		$item = Item::create();
		$item->save($item_datas);
		//Request the tested method
		$cost = $remote->shipping($items);
		//Delete Temporary Documents
		Item::remove(array("_id" => $item["_id"]));
		//Test result
		$this->assertEqual( 0 , $cost);
	}

	public function testOverSizeShipping() {
		$item_id = new MongoId('4ddsqsdqszzz80f3ad53892614080076e0');
		$remote = new OrderMock();
		$items = array(
			"0" => array(
				"_id" => new MongoId("4ddsqsdqszzz80f3ad53892614080076e0"),
				"category" => "Room D\u00e9cor",
				"color" => "",
				"description" => "test",
				"discount_exempt" => true,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string) $item_id,
				"primary_image" => "4d015488ce64e5c072fc1e00",
				"product_weight" => 0.64,
				"quantity" => 5,
				"sale_retail" => 3,
				"size" => "no size",
				"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
				"event_name" => "Babyganics",
				"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed"
		));
		$item_datas = array(
		  "_id" => $item_id,
		  "category" => "Room D\u00e9cor",
		  "color" => "",
		  "created_date" => "Wed, 16 Mar 2011 13:30:21 -0400",
		  "description" => "test",
		  "details" => array (
		    "no size" => 3
		  ),
		  "discount_exempt" => true,
		  "enabled" => true,
		  "event" => array(
		    "4cfdfdfdfdfd1dd1ce64e5300aeb4100"
		  ),
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_details" => array(
		    "no size" => array(
		      "sale_count" => 1
		    )
		  ),
		  "sale_retail" => 168,
		  "shipping_exempt" => false,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "total_quantity" => 3,
		  "url" => "url_test",
		  "vendor" => "fdeeee",
		  "vendor_style" => "SDDSER.SO16",
		  "views" => 3
		);
		$item = Item::create();
		$item->save($item_datas);
		//Request the tested method
		$cost = $remote->overSizeShipping($items);
		//Delete Temporary Documents
		Item::remove(array("_id" => $item["_id"]));
		//Test result
		$this->assertEqual( 6 , $cost);
	}

	public function testTax() {
		$item_id = new MongoId('4ddsqsdqszzz80f3ad53892614080076e0');
		$order_id = '8788727dsds3782738dsdsds728';
		$user_id = '787878787zazazag78dsdsdsds78';
		$remote = new OrderMock();
		$items = array(
			"0" => array(
				"_id" => new MongoId("4ddsqsdqszzz80f3ad53892614080076e0"),
				"category" => "Room D\u00e9cor",
				"color" => "",
				"description" => "test",
				"discount_exempt" => true,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string) $item_id,
				"primary_image" => "4d015488ce64e5c072fc1e00",
				"product_weight" => 0.64,
				"quantity" => 5,
				"cancel" => false,
				"sale_retail" => 3,
				"size" => "no size",
				"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
				"event_name" => "Babyganics",
				"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed",
				"tax" => 1,
				"taxable" => true
		));
		$order_datas = array(
			"_id" => new MongoId($order_id),
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => "4d038da6ce64e5973e8a1500",
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => "4cffa43ace64e5ae3e181900",
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 3,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => $user_id
		);
		$current_order = array(
			"id" => $order_id,
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => "4d038da6ce64e5973e8a1500",
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => "4cffa43ace64e5ae3e181900",
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 3,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "11211",
				"isAjax" => "1",
				"user_id" => $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => $user_id
		);
		$order = OrderMock::create();
		$order->save($order_datas);
		//Request the tested method
		$result = $remote->tax($current_order, $items);
		//Delete Temporary Documents
		OrderMock::remove(array("_id" => $order_id));
		//Test result
		$this->assertEqual( 3 , $result);
	}

	public function testSubTotal() {
		$remote = new OrderMock();
		$item_id = new MongoId('4ddsqsdqszzz80f3ad53892614080076e0');
		$items = array(
			"0" => array(
				"_id" => new MongoId("4ddsqsdqszzz80f3ad53892614080076e0"),
				"category" => "Room D\u00e9cor",
				"color" => "",
				"description" => "test",
				"discount_exempt" => true,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string) $item_id,
				"primary_image" => "4d015488ce64e5c072fc1e00",
				"product_weight" => 0.64,
				"quantity" => 5,
				"cancel" => false,
				"sale_retail" => 3,
				"size" => "no size",
				"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
				"event_name" => "Babyganics",
				"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed",
				"tax" => 1,
				"taxable" => true
		));
		//Request the tested method
		$result = $remote->subTotal($items);
		//Test result
		$this->assertEqual( 15 , $result);
	}

	public function testSaveCurrentOrder() {
		$author = "test";
		$comment = "commment @test !";

		$data = array(
			"active" => 1,
			"created_on" => "Wed, 22 Sep 2010 16: 50: 44 -0400",
			"email" => uniqid('test') . '@example.com',
			"firstname" => "KLKL",
			"invitation_codes" => array(
			"0" => "fdfdfdddd"
			),
			"invited_by" => "fdfdfd",
			"lastip" => "204.246.230.160",
			"lastlogin" => "Thu, 10 Mar 2011 22: 42: 08 -0500",
			"lastname" => "OPOo",
			"legacy" => 0,
			"logincounter" => 9,
			"password" => "0b505f152dc80b527035e3500925936fe9703d2c",
			"purchase_count" => 2,
			"reset_token" => "0",
			"total_credit" => 0
		);
		$user = User::create($data);
		$result = $user->save();
		$userId = $user->_id;

		$data = array(
			'category' => 'Room D\u00e9cor',
			'color' => '',
			'description' => 'test',
			'discount_exempt' => true,
			'expires' => array(
				'sec' => 1292079402,
				'usec' => 0
			),
			'product_weight' => 0.64,
			'quantity' => 5,
			'initial_quantity' => 5,
			'cancel' => false,
			'sale_retail' => 3,
			'size' => 'no size',
			'url' => 'babyganics-alcohol-free-hand-sanitizer-250ml',
			'event_name' => 'Babyganics',
			'event_id' => '4cfdfdfdfdfd1dd1ce64e5300aeb4100',
			'line_number' => 0,
			'status' => 'Order Placed',
			'tax' => 1,
			'taxable' => true
		);
		$item1 = Item::create($data);
		$item1->save();
		$item1Id = $item1->_id;

		$data = array(
			'category' => 'RoomDSDS D\u00e9cor',
			'color' => '',
			'description' => 'test2',
			'discount_exempt' => true,
			'expires' => array(
				'sec' => 1292079402,
				'usec' => 0
			),
			'primary_image' => '4d015488ce64e5c072fc1e00',
			'product_weight' => 0.64,
			'quantity' => 1,
			'initial_quantity' => 2,
			'cancel' => true,
			'sale_retail' => 3,
			'size' => 'no size',
			'url' => 'babyganics-alcohol-free-hand-sanitizer-250ml',
			'event_name' => 'Babyganics',
			'event_id' => '4cfdfdfdfdfd1dd1ce64e5300aeb4100',
			'line_number' => 0,
			'status' => 'Order Placed',
			'tax' => 1,
			'taxable' => true
		);
		$item2 = Item::create($data);
		$item2->save();
		$item2Id = $item2->_id;

		$items = array(
			$item1->data(),
			$item2->data()
		);

		$data = array(
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => $items,
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => (string) $userId
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => (string) $userId
		);
		$order = OrderMock::create($data);
		$order->save();
		$orderId = $order->_id;

		$data = array(
			"id" => (string) $orderId,
			'total' => 7.95,
			'subTotal' => 3.25,
			'tax' => 0,
			'handling' => 7.95,
			'promocode_disable' => false,
			'credit_used' => -3.25,
			'comment' => $comment,
			'user_id' => (string) $userId,
			'user_total_credits' => 1.75
		);
		$remote = new OrderMock();
		$result = $remote->saveCurrentOrder($data, $items, $author);

		$user = User::first(array('conditions' => array('_id' => $userId)));
		$order = OrderMock::first(array('conditions' => array('_id' => $orderId)));

		$expected = $data['user_total_credits'];
		$result = $user['total_credit'];
		$this->assertEqual($expected, $result);

		$expected = $data['total'];
		$result = $order['total'];
		$this->assertEqual($expected, $result);

		$expected = $data['subTotal'];
		$result = $order['subTotal'];
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = $order['items'][1]['quantity'];
		$this->assertEqual($expected, $result);

		$result = $order['items'][1]['cancel'];
		$this->assertTrue($result);

		User::remove(array("_id" => $userId));
		OrderMock::remove(array("_id" => $orderId));
		Item::remove(array("_id" => $item1Id));
		Item::remove(array("_id" => $item2Id));
	}

	public function testCancelItem() {
		$orderCollection = OrderMock::collection();
		$result = true;
		$item_id = new MongoId('4ddsqsdqszzz80f3ad53892614080076e0');
		$order_id = new MongoId('8788727dsds3782738dsdsds728');
		$remote = new OrderMock();
		$order_datas = array(
			"_id" => $order_id,
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => (string) $item_id,
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => (string) $item_id,
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 3,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1"
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65
		);
		$order = OrderMock::create();
		$order->save($order_datas);
		//Request the tested method
		$remote->cancelItem((string) $order_id, (string) $item_id, true);
		//Test result
		$order = $orderCollection->findOne(array("_id" => $order_id));
		if($order["items"][0]["cancel"] != true) {
			$result = false;
		}
		OrderMock::remove(array("_id" => $order_id));
		//Test result
		$this->assertEqual( true , $result);
	}

	public function testChangeQuantity() {
		$orderCollection = OrderMock::collection();
		$result = true;
		$item_id = new MongoId('4ddsqsdqszzz80f3ad53892614080076e0');
		$order_id = new MongoId('8788727dsds3782738dsdsds728');
		$remote = new OrderMock();
		$order_datas = array(
			"_id" => $order_id,
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => (string) $item_id,
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => (string) $item_id,
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 3,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1"
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65
		);
		$order = OrderMock::create();
		$order->save($order_datas);
		$remote->changeQuantity((string) $order_id, (string) $item_id, 2, 5);
		$order = $orderCollection->findOne(array('_id' => $order_id));

		$expected = 2;
		$result = $order['items'][0]['quantity'];
		$this->assertEqual($expected, $result);

		$expected = 5;
		$result = $order['items'][0]['initial_quantity'];
		$this->assertEqual($expected, $result);

		OrderMock::remove(array('_id' => $order_id));
	}

	public function testRefreshTempOrder() {
		$orderCollection = OrderMock::collection();
		$result = true;
		$item_id = new MongoId('4ddsqsdqszzz80f3ad53892614080076e0');
		$order_id = new MongoId('8788727dsds3782738dsdsds728');
		$item_id_2 = new MongoId('0920909Z200IAOIOIZOAIIiioioioio');
		$user_id = new MongoId('787878787zazazag78dsdsdsds78');
		$remote = new OrderMock();
		$selected_order = array(
			"id" => (string) $order_id,
			'total' => 7.95,
			'subTotal' => 3.25,
			'tax' => 0,
			'handling' => 7.95,
			'promo_discount' => 0,
			'promocode_disable' => false,
			'credit_used' => -3.25,
			'user_id' => (string) $user_id,
			'user_total_credits' => 1.75,
			"promo_code" => "weekend10",
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => (string) $user_id
			)
		);
		$items = array(
		"0" => array(
			"_id" => (string) $item_id,
			"category" => "Room D\u00e9cor",
			"color" => "",
			"description" => "test",
			"discount_exempt" => true,
			"expires" => array(
				"sec" => 1292079402,
				"usec" => 0
			),
			"item_id" => (string) $item_id,
			"primary_image" => "4d015488ce64e5c072fc1e00",
			"product_weight" => 0.64,
			"quantity" => 5,
			"initial_quantity" => 5,
			"cancel" => false,
			"sale_retail" => 3,
			"size" => "no size",
			"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
			"event_name" => "Babyganics",
			"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
			"line_number" => 0,
			"status" => "Order Placed",
			"tax" => 1,
			"taxable" => true
		),
		"1" => array(
			"_id" => (string) $item_id_2,
			"category" => "RoomDSDS D\u00e9cor",
			"color" => "",
			"description" => "test2",
			"discount_exempt" => true,
			"expires" => array(
				"sec" => 1292079402,
				"usec" => 0
			),
			"item_id" => (string) $item_id_2,
			"primary_image" => "4d015488ce64e5c072fc1e00",
			"product_weight" => 0.64,
			"quantity" => 1,
			"initial_quantity" => 2,
			"cancel" => true,
			"sale_retail" => 3,
			"size" => "no size",
			"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
			"event_name" => "Babyganics",
			"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
			"line_number" => 0,
			"status" => "Order Placed",
			"tax" => 1,
			"taxable" => true
		));
		$order_datas = array(
			"_id" => $order_id,
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => (string) $item_id,
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => (string) $item_id,
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 3,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			),
			"1" => array(
				"_id" => (string)$item_id_2,
				"category" => "Baby Gear",
				"color" => "",
				"description" => "TESTSTYTYSTYT",
				"discount_exempt" => false,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string)$item_id_2,
				"primary_image" => "4d015488ce64e5c072fc1e00",
				"product_weight" => 0.64,
				"quantity" => 2,
				"sale_retail" => 3,
				"size" => "no size",
				"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
				"event_name" => "Babyganics",
				"event_id" => "4cfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => (string) $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => (string) $user_id
		);
		$item_datas = array(
		  "_id" => $item_id,
		  "category" => "Room D\u00e9cor",
		  "color" => "",
		  "created_date" => "Wed, 16 Mar 2011 13:30:21 -0400",
		  "description" => "test",
		  "details" => array (
		    "no size" => 3
		  ),
		  "discount_exempt" => true,
		  "enabled" => true,
		  "event" => array(
		    "4cfdfdfdfdfd1dd1ce64e5300aeb4100"
		  ),
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_details" => array(
		    "no size" => array(
		      "sale_count" => 1
		    )
		  ),
		  "sale_retail" => 168,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "total_quantity" => 3,
		  "url" => "url_test",
		  "vendor" => "fdeeee",
		  "vendor_style" => "SDDSER.SO16",
		  "views" => 3
		);
		$item_datas_2 = array(
		  "_id" => $item_id_2,
		  "category" => "Room D\u00e9cor",
		  "color" => "",
		  "created_date" => "Wed, 16 Mar 2011 13:30:21 -0400",
		  "description" => "test",
		  "details" => array (
		    "no size" => 3
		  ),
		  "discount_exempt" => true,
		  "enabled" => true,
		  "event" => array(
		    "4cfdfdfdfdfd1dd1ce64e5300aeb4100"
		  ),
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_details" => array(
		    "no size" => array(
		      "sale_count" => 1
		    )
		  ),
		  "sale_retail" => 168,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "total_quantity" => 3,
		  "url" => "url_test",
		  "vendor" => "fdeeee",
		  "vendor_style" => "SDDSER.SO16",
		  "views" => 3
		);
		$item = Item::create();
		$item->save($item_datas);
		$item2 = Item::create();
		$item2->save($item_datas_2);
		$order = OrderMock::create();
		$order->save($order_datas);
		//Request the tested method
		$result = $remote->refreshTempOrder($selected_order, $items);
		//Test result
		OrderMock::remove(array("_id" => $order_id));
		//Test result
		$this->assertEqual( true , $result);
	}
}

?>