<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\controllers\OrdersController;
use app\tests\mocks\controllers\OrdersControllerMock;
use app\tests\mocks\models\OrderMock;
use app\models\User;
use app\models\Event;
use app\models\Item;
use app\models\Address;
use app\models\OrderShipped;
use MongoId;
use MongoDate;
use lithium\storage\Session;

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

		$this->controller = new OrdersControllerMock(array(
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

		$result = is_int($return['shipDate'][(string) $order->_id]);
		$this->assertTrue($result);

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

		$event->delete();
		$order->delete();
		$orderShipped->delete();
	}

	public function testView() {
		$address = $this->_address();

		$data = array(
			'title' => 'test',
			'end_date' => $shipDate = new MongoDate(strtotime('+1 week'))
		);
		$event = Event::create($data);
		$event->save();

		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => (string) $id,
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

		$return = $this->controller->view((string) $order->_id);

		$result = isset($return['order']);
		$this->assertTrue($result);

		$result = isset($return['orderEvents']);
		$this->assertTrue($result);

		$result = isset($return['itemsByEvent']);
		$this->assertTrue($result);

		$result = isset($return['new']);
		$this->assertTrue($result);

		$result = isset($return['shipDate']);
		$this->assertTrue($result);

		$result = isset($return['allEventsClosed']);
		$this->assertTrue($result);

		$result = isset($return['shipped']);
		$this->assertTrue($result);

		$result = isset($return['preShipment']);
		$this->assertTrue($result);

		$result = isset($return['spinback_fb']);
		$this->assertTrue($result);

		$result = isset($return['shipRecord']);
		$this->assertTrue($result);

		$result = isset($return['openEvent']);
		$this->assertTrue($result);

		$result = isset($return['savings']);
		$this->assertTrue($result);

		$order->delete();
	}

	public function testShippingWithoutData() {
		$return = $this->controller->shipping();

		$result = array_key_exists('address', $return);
		$this->assertTrue($result);

		$result = isset($return['addresses_ddwn']);
		$this->assertTrue($result);

		$result = array_key_exists('shipDate', $return);
		$this->assertTrue($result);

		$result = isset($return['cartEmpty']);
		$this->assertTrue($result);

		$result = array_key_exists('error', $return);
		$this->assertTrue($result);

		$result = array_key_exists('selected', $return);
		$this->assertTrue($result);

		$result = isset($return['cartExpirationDate']);
		$this->assertTrue($result);
	}

	public function testShippingTriggeringSaveCreatingAddress() {
		$address = $this->_address();
		$this->controller->request->data = array(
			'opt_save' => '1',
			'address_id' => '',
			'telephone' => '800-999-5555',
			'state' => 'CA'
		) + $address;

		$return = $this->controller->shipping();

		$expected = 'Orders::payment';
		$result = $this->controller->redirect[0][0];
		$this->assertEqual($expected, $result);

		$expected = $address['address'];
		$result = Address::first(array(
			'conditions' => array(
				'address' => $address['address']
			)
		))->address;
		$this->assertEqual($expected, $result);
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