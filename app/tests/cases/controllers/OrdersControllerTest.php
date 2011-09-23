<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\controllers\OrdersController;
use app\tests\mocks\models\OrderMock;
use app\models\User;
use MongoId;
use MongoDate;
use lithium\storage\Session;
use app\models\Item;
use app\models\OrderShipped;

class OrdersControllerTest extends \lithium\test\Unit {

	public $controller;

	public $user;

	protected $_backup = array();

	protected $_delete = array();

	public function setUp() {
		Session::config(array(
			'default' => array('adapter' => 'Memory'),
			'cookie' => array('adapter' => 'Memory')
		));

		$this->controller = new OrdersController(array(
			'request' => new Request(),
			'classes' => array(
				'tax' => 'app\tests\mocks\extensions\AvaTaxMock',
				'order' => 'app\tests\mocks\models\OrderMock',
				'affiliate' => 'app\tests\mocks\models\AffiliateMock'
			)
		));

		$data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'email' => uniqid('george') . '@example.com'
		);
		$this->user = User::create();
		$this->user->save($data, array('validate' => false));

		$this->_delete[] = $this->user;
		$session = $this->user->data();
		Session::write('userLogin', $session);
	}

	public function tearDown() {
		Session::delete('userLogin');

		foreach ($this->_delete as $document) {
			$document->delete();
		}
		// PaymentsMock::resetMock();
	}

	public function testIndex() {
		$address = $this->_address();

		$data = array(
			'handling' => 7.95,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'user_id' => (string) $this->user->_id,
			'date_created' => new MongoDate(),
			'items' => array()
		);
		$order = OrderMock::create($data);
		$order->save(null, array('validate' => false));

		$return = $this->controller->index();

		$result = isset($return['orders']);
		$this->assertTrue($result);

		$result = array_key_exists('shipDate', $return);
		$this->assertTrue($result);

		$result = isset($return['trackingNumbers']);
		$this->assertTrue($result);

		$result = isset($return['lifeTimeSavings']);
		$this->assertTrue($result);

		$expected = 0;
		$result = $return['lifeTimeSavings'];
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testIndexWithAttachedItems() {
		$address = $this->_address();

		$data = array(
			'title' => 'test',
			'end_date' => $shipDate = new MongoDate(strtotime('+1 week'))
		);
		$event = Event::create($data);
		$event->save();

		$data = array(
			'handling' => 7.95,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'user_id' => (string) $this->user->_id,
			'date_created' => new MongoDate(),
			'items' => array(
				array('event_id' => $event->_id) + $this->_item()->data()
			)
		);
		$order = OrderMock::create($data);
		$order->save(null, array('validate' => false));

		$data = array(
			'OrderId' => $order->_id,
			'Tracking #' => 'number-a',
		);
		$orderShipped = OrderShipped::create($data);
		$orderShipped->save(null, array('validate' => false));

		$return = $this->controller->index();

		$expected = -15;
		$result = $return['lifeTimeSavings'];
		$this->assertEqual($expected, $result);

		$expected = array(
			(string) $order->_id => null
		);
		$result = $return['shipDate'];
		$this->assertEqual($expected, $result);

		$expected = array(
			(string) $order->_id => array(
				array(
					'code' => 'number-a',
					'method' => 'UPS'
				)
			)
		);
		$result = $return['trackingNumbers'];
		$this->assertEqual($expected, $result);

		$order->delete();
		$orderShipped->delete();
	}

	protected function _address() {
		return array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'address' => 'Hollywood Blvd ' . uniqid(),
			'city' => 'Lost Angeles',
			'zip' => '90001',
			'state' => 'California',
			'country' => 'USA'
		);
	}

	protected function _item() {
		$data = array(
			'_id' => $id = new MongoId(),
			'item_id' => $id,
			'category' => 'Alpha',
			'color' => 'green',
			'description' => 'Test 250ml',
			'discount_exempt' => false,
			'expires' => array(
				'sec' => 1292079402,
				'usec' => 0
			),
			'product_weight' => 0.64,
			'quantity' => 5,
			'sale_retail' => 3,
			'size' => 'no size',
			'line_number' => 0,
			'status' => 'Order Placed'
		);
		$item = Item::create($data);
		$item->save();

		return $this->_delete[] = $item;
	}
}

?>