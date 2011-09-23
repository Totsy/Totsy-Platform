<?php

namespace admin\tests\cases\controllers;

use lithium\action\Request;
use admin\controllers\OrdersController;
use admin\tests\mocks\models\OrderMock;
use admin\models\Event;
use admin\models\Order;
use admin\models\User;
use admin\models\Item;
use MongoId;
use MongoDate;
use lithium\storage\Session;

class OrdersControllerTest extends \lithium\test\Unit {

	public $controller;

	public function setUp() {
		Session::config(array(
			'default' => array('adapter' => 'Memory')
		));

		$this->controller = new OrdersController(array(
			'request' => new Request(),
			'classes' => array(
				'tax' => 'admin\tests\mocks\extensions\AvaTaxMock',
				'order' => 'admin\tests\mocks\models\OrderMock'
			)
		));
	}

	public function testIndexWithoutData() {
		$result = $this->controller->index();
		$this->assertTrue(!empty($result['headings']));
	}

	public function testIndex() {
		$data = array(
			'title' => 'test',
			'end_date' => $shipDate = new MongoDate(strtotime('+1 week'))
		);
		$event = Event::create($data);
		$event->save();

		$items[] = array(
			'event_id' => $event->_id
		);
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'_test' => 'a',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'shipping' => array(
				'firstname' => 'George',
				'lastname' => 'Opossum',
				'address' => 'Venice Rd'
			),
			'items' => $items
		);
		$order1 = Order::create($data);
		$order1->save(null, array('validate' => false));

		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'_test' => 'b',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'billing' => array(
				'firstname' => 'Leonardo',
				'lastname' => 'di Caprio',
				'address' => 'Dreams Blvd'
			),
			'items' => $items
		);
		$order2 = Order::create($data);
		$order2->save(null, array('validate' => false));

		/* @fixme This currently fails as you cannot apply a regex on an id. */
		/*
		$this->controller->request->data = array(
			'search' => (string) $order1->_id,
			'type' => 'order'
		);
		$result = $this->controller->index();
		$this->assertTrue(!empty($result['orders']));
		*/

		$this->controller->request->data = array(
			'search' => 'Dreams',
			'type' => 'address'
		);
		$result = $this->controller->index();
		$this->assertTrue(!empty($result['orders']));
		$this->assertTrue(!empty($result['shipDate']));

		$order1->delete();
		$order2->delete();
	}

	public function testCancel() {
		$order_id = new MongoId("8788727dsds3782738dsdsds728");
		$user_id = new MongoId("787878787zazazag78dsdsdsds78");
		$item_id = new MongoId("4ddsqsdqszzz80f3ad53892614080076e0");
		$comment = "Comment @ Test";

		$remote = $this->controller;

		$remote->request->data = array('id' => (string) $order_id, 'comment' => $comment);
		$remote->request->params['type'] = 'html';
		$user = Session::read('userLogin');
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
				"isAjax" => "1",
				"user_id" => (string) $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => (string) $user_id
		);
		$user_datas = array(
			"_id" => $user_id,
			"active" => 1,
			"created_on" => "Wed, 22 Sep 2010 16: 50: 44 -0400",
			"email" => uniqid('test') . 'example.com',
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
		$user = User::create();
		$user->save($user_datas);
		$order = OrderMock::create();
		$order->save($order_datas);

		$remote->cancel();

		$result_order = OrderMock::find('first', array('conditions' => array(
			'_id' => $order["_id"]
		)));
		$order = $result_order->data();

		$result = $order["cancel"];
		$this->assertTrue($result);

		foreach($order["items"] as $item) {
			$result = $item["cancel"];
			$this->assertTrue($result);
		}

		$result_user = User::find('first', array('conditions' => array(
			'_id' => $user["_id"]
		)));
		$this->assertTrue($result_user);

		$this->skipIf(!is_object($result_user), "Can't continue result is not an object.");

		$user = $result_user->data();
		foreach ($order["modifications"] as $modif) {
			$expected = $comment;
			$result = $modif['comment'];
			$this->assertEqual($expected, $result);
		}

		OrderMock::remove(array("_id" => $order_id));
		User::remove(array("_id" => $user_id));
	}

	public function testManageItemsUnsaved() {
		$data = array(
			"active" => 1,
			"email" => uniqid('test') . '@example.com',
			"firstname" => "Test",
			"lastname" => "User",
			"total_credit" => 0
		);
		$user = User::create($data);
		$result = $user->save();
		$this->assertTrue($result);

		$userId = $user->_id;

		$data = array(
		  "discount_exempt" => true,
		  "enabled" => true,
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_retail" => 10,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "tax" => 1,
		  "total_quantity" => 5,
		);
		$item = Item::create($data);
		$result = $item->save();
		$this->assertTrue($result);

		$item1Id = $item->_id;

		$data = array(
		  "description" => "test",
		  "discount_exempt" => true,
		  "enabled" => true,
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_retail" => 13,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "tax" => 1,
		  "total_quantity" => 2,
		);
		$item = Item::create($data);
		$result = $item->save();
		$this->assertTrue($result);

		$item2Id = $item->_id;

		$data = array(
			"authKey" => "090909099909",
			"credit_used" => -5,
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => (string) $item1Id,
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => (string) $item1Id,
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 10,
					"line_number" => 0,
					"status" => "Order Placed"
			),
			"1" => array(
				"_id" => (string) $item2Id,
				"discount_exempt" => false,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string) $item2Id,
				"product_weight" => 0.64,
				"quantity" => 2,
				"sale_retail" => 13,
				"size" => "no size",
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
				"user_id" => (string) $userId
			),
			"shippingMethod" => "ups",
			"subTotal" => 76,
			"tax" => 0,
			"total" => 92.7,
			"user_id" => (string) $userId
		);
		$order = OrderMock::create($data);
		$result = $order->save($data);
		$this->assertTrue($result);

		$orderId = $order->_id;

		$items = array(
			'0' => array(
				'cancel' => 'true',
				'initial_quantity' => '5',
				'quantity' => '5'
				),
			'1' => array(
				'cancel' => '',
				'initial_quantity' => '2',
				'quantity' => '1'
		));
		$comment = "Comment @ Test";

		$data = array(
			'id' => (string) $orderId,
			'comment' => $comment,
			'items' => $items,
			'total' => 92.7,
			'subTotal' => 76,
			'tax' => 0,
			'handling' => 7.95,
			'promocode_disable' => false,
			'credit_used' => -3.25,
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			'comment' => $comment,
			'user_id' => (string) $userId,
			'user_total_credits' => 1.75,
			'save' => 'false'
		);

		$this->controller->request->data = $data;
		$this->controller->request->params['type'] = 'html';
		$result = $this->controller->manage_items();

		$this->assertTrue($result->items[0]->cancel);
		$this->assertFalse($result->items[1]->cancel);

		$expected = 29.7;
		$result = $result->total;
		$this->assertEqual($expected, $result);

		OrderMock::remove(array("_id" => $orderId));
		User::remove(array("_id" => $userId));
		Item::remove(array("_id" => $item1Id));
		Item::remove(array("_id" => $item2Id));
	}

	public function testManageItemsSaved() {
		$order_id = new MongoId("8788727dsds3782738dsdsds728");
		$user_id = new MongoId("787878787zazazag78dsdsdsds78");
		$item_id = new MongoId("4ddsqsdqszzz80f3ad53892614080076e0");
		$item_id_2 = new MongoId("0920909Z200IAOIOIZOAIIiioioioio");
		$comment = "Comment @ Test";
		$user = Session::read('userLogin');

		$items = array(
			'0' => array(
				'cancel' => 'true',
				'initial_quantity' => '5',
				'quantity' => '5'
				),
			'1' => array(
				'cancel' => '',
				'initial_quantity' => '2',
				'quantity' => '1'
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
					"sale_retail" => 10,
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
				"sale_retail" => 13,
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
			"subTotal" => 76,
			"tax" => 0,
			"total" => 92.7,
			"user_id" => (string) $user_id
		);
		$user_datas = array(
			"_id" => $user_id,
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
		$user = User::create();
		$user->save($user_datas);
		$order = OrderMock::create();
		$order->save($order_datas);

		$remote = $this->controller;

		$datas = array(
			'id' => (string) $order_id,
			'comment' => $comment,
			'items' => $items,
			'total' => 29.7,
			'subTotal' => 13,
			'tax' => 0,
			'handling' => 7.95,
			'promocode_disable' => false,
			'credit_used' => -3.25,
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			'comment' => $comment,
			'user_id' => (string) $user_id,
			'user_total_credits' => 1.75,
			'save' => 'true'
			);
		$remote->request->data = $datas;
		$remote->request->params['type'] = 'html';
		$result = $remote->manage_items();
		$selected_order = $result->data();

		$result = $selected_order["items"][0]["cancel"];
		$this->assertTrue($result);

		$expected = 29.7;
		$result = $selected_order['total'];
		$this->assertEqual($expected, $result);

		OrderMock::remove(array("_id" => $order_id));
		User::remove(array("_id" => $user_id));
		Item::remove(array("_id" => $item_id));
		Item::remove(array("_id" => $item_id_2));
	}

	public function testUpdateShippingWithoutData() {
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'date_created' => new MongoDate(strtotime('August 3, 2011'))
		);
		$order = Order::create($data);
		$order->save(null, array('validate' => false));

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);
	}

	public function testUpdateShippingWithMissing() {
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'date_created' => new MongoDate(strtotime('August 3, 2011'))
		);
		$order = Order::create($data);
		$order->save(null, array('validate' => false));

		$this->controller->request->data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'address' => 'Cloud St',
			'city' => null,
			'state' => 'California',
			'zip' => '9100',
			'phone' => '01234567890'
		);

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);

		$this->controller->request->data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'address' => 'Cloud St'
		);

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);

		$order->delete();
	}

	public function testUpdateShippingAddsModificationAndShipping() {
		Session::write('userLogin', array(
			'email' => uniqid('test') . '@example.com'
		));
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'shipping' => $shipping = array(
				'firstname' => 'George',
				'lastname' => 'Lucas',
				'address' => 'Cloud St',
				'city' => 'LA',
				'state' => 'California',
				'zip' => '9100',
				'phone' => '01234567890'
			)
		);
		$order = Order::create($data);
		$order->save(null, array('validate' => false));

		$this->controller->request->data = $shippingNew = array(
			'firstname' => 'Luke',
			'lastname' => 'Skywalker',
			'address' => 'Cloud St',
			'city' => 'LA',
			'state' => 'California',
			'zip' => '9100',
			'phone' => '01234567890'
		);

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);

		$order = Order::first((string) $order->_id);

		$result = !empty($order->modifications);
		$this->assertTrue($result);

		$result = filter_var($order->modifications[0]['author'], FILTER_VALIDATE_EMAIL);
		$this->assertTrue($result);

		$expected = $shipping;
		$result = $order->modifications[0]['old_datas']->data();
		$this->assertEqual($expected, $result);

		$expected = $shippingNew;
		$result = $order->shipping->data();
		$this->assertEqual($expected, $result);

		Session::delete('currentUser');
		$order->delete();
	}
}

?>