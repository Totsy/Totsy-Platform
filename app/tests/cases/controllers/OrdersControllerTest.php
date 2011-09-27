<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\controllers\OrdersController;
use app\tests\mocks\controllers\OrdersControllerMock;
use app\tests\mocks\models\OrderMock;
use app\tests\mocks\storage\session\adapter\MemoryMock;
use app\tests\mocks\extensions\PaymentsMock;
use app\models\User;
use app\models\Event;
use app\models\Item;
use app\models\Address;
use app\models\Cart;
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
		$adapter = new MemoryMock();

		Session::config(array(
			'default' => compact('adapter'),
			'cookie' => compact('adapter')
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

	public function testReviewRedirectsWithoutSessionData() {
		$address = $this->_address();

		$this->controller->review();

		$result = $this->controller->redirect[0];
		$expected = array('Orders::shipping');
		$this->assertEqual($expected, $result);

		Session::write('shipping', $address);
		$this->controller->review();

		$result = $this->controller->redirect[0];
		$expected = array('Orders::payment');
		$this->assertEqual($expected, $result);
	}

	public function testReviewWithoutData() {
		$adapter = new MemoryMock();

		Session::config(array(
			'default' => compact('adapter'),
			'cookie' => compact('adapter')
		));
		$session = $this->user->data();
		Session::write('userLogin', $session);

		$address = $this->_address();
		$sessionKey = Session::key('default');

		Session::write('shipping', $address);
		Session::write('billing', $address);
		Session::write('cc_infos', $this->_card(true));

		$data = array(
			'title' => 'test',
			'end_date' => new MongoDate(strtotime('+1 week'))
		);
		$event = Event::create($data);
		$event->save(null, array('validate' => false));

		$item = $this->_item();

		$data = array(
			'user' => (string) $this->user->_id,
			'session' => $sessionKey,
			'expires' => new MongoDate(strtotime('+1 week')),
			'url' => 'test',
			'primary_image' => 'test',
			'event' => array(
				(string) $event->_id
			)
		) + $item->data();

		$cart = Cart::create($data);
		$cart->save(null, array('validate' => false));

		$return = $this->controller->review();

		$expected = array(
			'cartPromo',
			'cartCredit',
			'services',
			'postDiscountTotal',
			'user',
			'cart',
			'total',
			'subTotal',
			'creditCard',
			'tax',
			'shippingCost',
			'overShippingCost',
			'billingAddr',
			'shippingAddr',
			'cartEmpty',
			'shipDate',
			'savings',
			'credits',
			'cartExpirationDate',
			'promocode_disable'
		);
		$result = array_keys($return);
		$this->assertFalse(array_diff($expected, $result));
		$this->assertFalse(array_diff($result, $expected));

		$result = $this->controller->redirect;
		$this->assertFalse($result);

		$result = Session::read('cc_error');
		$this->assertFalse($result);

		$cart->delete();
		$event->delete();
	}

	public function testReviewWithData() {
		$address = $this->_address() + array('address2' => 'c/o Skywalker');
		$sessionKey = Session::key('default');

		Session::write('shipping', $address);
		Session::write('billing', $address);
		Session::write('cc_infos', $this->_card(true));

		$data = array(
			'title' => 'test',
			'end_date' => new MongoDate(strtotime('+1 week'))
		);
		$event = Event::create($data);
		$event->save(null, array('validate' => false));

		$item = $this->_item();

		$data = array(
			'user' => (string) $this->user->_id,
			'session' => $sessionKey,
			'expires' => new MongoDate(strtotime('+1 week')),
			'url' => 'test',
			'primary_image' => 'test',
			'event' => array(
				(string) $event->_id
			)
		) + $item->data();

		$cart = Cart::create($data);
		$cart->save(null, array('validate' => false));

		$this->controller->request->data = array(
			'process' => array(
				'test'
			)
		);
		$return = $this->controller->review();

		$expected = 'Orders::view';
		$result = $this->controller->redirect[0][0];
		$this->assertEqual($expected, $result);

		$result = Session::read('cc_error');
		$this->assertFalse($result);

		$result = PaymentsMock::$authorize[1];
		$expected = 15;
		$this->assertEqual($expected, $result);

		$cart->delete();
		$event->delete();
	}

	public function testPaymentRedirectsWithoutShipping() {
		$address = $this->_address();

		$this->controller->payment();

		$result = $this->controller->redirect[0];
		$expected = array('Orders::shipping');
		$this->assertEqual($expected, $result);
	}

	public function testPaymentWithoutData() {
		$address = $this->_address();

		Session::write('shipping', $address);
		Session::write('billing', $address);

		$return = $this->controller->payment();

		$expected = array(
			'address', 'addresses_ddwn',
			'selected',
			'cartEmpty',
			'payment',
			'shipping', 'shipDate',
			'cartExpirationDate'
		);
		$result = array_keys($return);
		$this->assertFalse(array_diff($expected, $result));
		$this->assertFalse(array_diff($result, $expected));

		$result = Session::check('billing');
		$this->assertTrue($result);

		$result = Session::check('cc_infos');
		$this->assertFalse($result);

		$result = Session::check('cc_error');
		$this->assertFalse($result);
	}

	public function testPaymentWithData() {
		$data = array(
			'telephone' => '800-999-5555',
			'state' => 'CA'
		) + $this->_address();

		$address = Address::create($data);
		$address->save(null, array('validate' => true));

		Session::write('shipping', $address->data());
		Session::write('billing', $address->data());

		$this->controller->request->data = array(
			'card_type' => 'visa',
			'card_number' => '4111111111111111',
			'card_month' => '11',
			'card_year' => '2023',
			'card_code' => '123',
			'address_id' => (string) $address->_id
		);

		$return = $this->controller->payment();

		$result = $this->controller->redirect;
		$this->assertFalse($result);

		$expected = array(
			'address', 'addresses_ddwn',
			'selected',
			'cartEmpty',
			'payment',
			'shipping', 'shipDate',
			'cartExpirationDate'
		);
		$result = array_keys($return);
		$this->assertFalse(array_diff($expected, $result));
		$this->assertFalse(array_diff($result, $expected));

		$result = $return['payment']->number;
		$expected = '4111111111111111';
		$this->assertEqual($expected, $result);

		$result = $return['payment']->code;
		$expected = '123';
		$this->assertEqual($expected, $result);

		$result = Session::check('billing');
		$this->assertTrue($result);

		$result = Session::check('cc_infos');
		$this->assertTrue($result);

		$result = Session::check('cc_error');
		$this->assertFalse($result);

		$address->delete();
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

	protected function _item($raw = false) {
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
	if ($raw) {
			return $data;
		}
		$item = Item::create($data);
		$item->save();

		return $this->_delete[] = $item;
	}

	protected function _card($raw = false) {
		$creditCard = array(
			 'number' => '4111111111111111',
			 'month' => 11,
			 'year' => 2023
		);
		if ($raw) {
			return $creditCard;
		}
		$billing = PaymentsMock::create('default', 'address', $this->_address());
		return PaymentsMock::create('default', 'creditCard', $creditCard + compact('billing'));
	}
}

?>