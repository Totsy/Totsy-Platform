<?php

namespace app\tests\cases\models;

use app\tests\mocks\models\OrderMock;
use app\tests\mocks\payments\ProcessorMock;
use app\tests\mocks\storage\session\adapter\MemoryMock;
use MongoId;
use lithium\storage\Session;
use app\models\User;
use app\models\Order;
use app\models\Credit;
use app\models\Promocode;
use app\models\Item;

class OrderTest extends \lithium\test\Unit {

	public $user;

	protected $_backup = array();

	protected $_delete = array();

	public function setUp() {
		$adapter = new MemoryMock();

		Session::config(array(
			'default' => compact('adapter')
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
		ProcessorMock::resetMock();
	}

	public function testDates() {
		$result = OrderMock::dates('now');
		$this->assertTrue(is_a($result, 'MongoDate'));
	}

	public function testSummary() {
		$data = array(
			'order_id' => $id = new MongoId('4e7a1b69ed08e0992a000002'),
			'total' => 12.3456
		);
		$order = OrderMock::create($data);

		$expected = array(
			(string) $id => "{$id}- Order Total: $12.35"
		);
		$result = $order->summary();
		$this->assertEqual($expected, $result);
	}

	public function testProcess() {
		$address = $this->_address();
		$address['address2'] = 'c/o Skywalker';

		$vars = array(
			'user' => $this->user,
			'creditCard' => $this->_card(true),
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 0,
			'cartCredit' => $this->_credit(),
			'cartPromo' => $this->_promocode()
		);
		$items = array(
			$this->_item()
		);
		$avatax = array(
			'tax' => 0
		);
		$data = array();

		$creditCard = array(
			 'number' => 'encrypted:4111111111111111',
			 'month' => 'encrypted:11',
			 'year' => 'encrypted:2023',
			 'type' => 'encrypted:visa'
		);
		Session::write('cc_infos', $creditCard);

		$result = OrderMock::process($data, $items, $vars, $avatax);
		$this->assertTrue($result);

		$result = Session::read('cc_error');
		$this->assertFalse($result);

		$expected = 123.45;
		$result = ProcessorMock::$authorize[1];
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['number'];
		$result = ProcessorMock::$authorize[2]->number;
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['month'];
		$result = ProcessorMock::$authorize[2]->month;
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['year'];
		$result = ProcessorMock::$authorize[2]->year;
		$this->assertEqual($expected, $result);
	}

	public function testRecordOrderWithoutService() {
		$authKey = '090909099909';
		$address = $this->_address();

		$data = array(
			'authKey' => $authKey,
			'handling' => 7.95,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'user_id' => $this->user->_id
		);
		$order = OrderMock::create($data);
		$result = $order->save();
		$this->assertTrue($result);

		$vars = array(
			'creditCard' => $this->_card(true),
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 0,
			'cartCredit' => $this->_credit(),
			'cartPromo' => $this->_promocode()
		);
		$items = array(
			$this->_item()
		);
		$avatax = array(
			'tax' => 0
		);

		$result = OrderMock::recordOrder(
			$vars,
			$items,
			$this->_card(false),
			$order,
			$avatax,
			$authKey,
			$items
		);
		$this->assertTrue($result);

		$result = Session::read('cc_error');
		$this->assertFalse($result);

		$expected = 123.45;
		$result = $order->total;
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = User::first((string) $this->user->_id)->purchase_count;
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testRecordOrderIncreasesUserPruchaseCount() {
		$authKey = '090909099909';
		$address = $this->_address();

		$data = array(
			'authKey' => $authKey,
			'handling' => 7.95,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'user_id' => $this->user->_id
		);
		$order = OrderMock::create($data);
		$result = $order->save();
		$this->assertTrue($result);

		$vars = array(
			'creditCard' => $this->_card(true),
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 0,
			'cartCredit' => $this->_credit(),
			'cartPromo' => $this->_promocode()
		);
		$items = array(
			$this->_item()
		);
		$avatax = array(
			'tax' => 0
		);
		OrderMock::recordOrder(
			$vars,
			$items,
			$this->_card(false),
			$order,
			$avatax,
			$authKey,
			$items
		);

		$expected = 1;
		$result = User::first((string) $this->user->_id)->purchase_count;
		$this->assertEqual($expected, $result);

		OrderMock::recordOrder(
			$vars,
			$items,
			$this->_card(false),
			$order,
			$avatax,
			$authKey,
			$items
		);

		$expected = 2;
		$result = User::first((string) $this->user->_id)->purchase_count;
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testRecordOrderWithFreeshipping() {
		$services = array(
			'freeshipping' => 'eligible'
		);
		Session::write('services', $services);

		$authKey = '090909099909';
		$address = $this->_address();

		$data = array(
			'authKey' => $authKey,
			'handling' => 7.95,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'discount' => 9,
			'user_id' => $this->user->_id
		);
		$order = OrderMock::create($data);
		$result = $order->save();
		$this->assertTrue($result);

		$vars = array(
			'creditCard' => $this->_card(true),
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 1,
			'cartCredit' => $this->_credit(),
			'cartPromo' => $this->_promocode()
		);
		$items = array(
			$this->_item()
		);
		$avatax = array(
			'tax' => 0
		);

		$result = OrderMock::recordOrder(
			$vars,
			$items,
			$this->_card(false),
			$order,
			$avatax,
			$authKey,
			$items
		);
		$this->assertTrue($result);

		$expected = 11;
		$result = $order->discount;
		$this->assertEqual($expected, $result);

		$expected = 10;
		$result = $order->handling;
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = $order->overSizeHandling;
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testRecordOrderFreeshippingFromPromocode() {
		$authKey = '090909099909';
		$address = $this->_address();

		$data = array(
			'authKey' => $authKey,
			'handling' => 7.95,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'user_id' => $this->user->_id
		);
		$order = OrderMock::create($data);
		$result = $order->save();
		$this->assertTrue($result);

		$promocode = $this->_promocode();
		$promocode->type = 'free_shipping';

		$vars = array(
			'creditCard' => $this->_card(true),
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 0,
			'cartCredit' => $this->_credit(),
			'cartPromo' => $promocode
		);
		$items = array(
			$this->_item()
		);
		$avatax = array(
			'tax' => 0
		);

		OrderMock::recordOrder(
			$vars,
			$items,
			$this->_card(false),
			$order,
			$avatax,
			$authKey,
			$items
		);

		$expected = 0;
		$result = $order->discount;
		$this->assertEqual($expected, $result);

		$expected = 10;
		$result = $order->handlingDiscount;
		$this->assertEqual($expected, $result);

		$expected = 10;
		$result = $order->handling;
		$this->assertEqual($expected, $result);

		$expected = 0;
		$result = $order->overSizeHandling;
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testRecordOrderWith10off50() {
		$services = array(
			'10off50' => 'eligible'
		);
		Session::write('services', $services);

		$authKey = '090909099909';
		$address = $this->_address();

		$data = array(
			'authKey' => $authKey,
			'handling' => 7.95,
			'ship_date' => 1294272000,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'discount' => 9,
			'user_id' => $this->user->_id
		);
		$order = OrderMock::create($data);
		$result = $order->save();
		$this->assertTrue($result);

		$vars = array(
			'creditCard' => $this->_card(true),
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 1,
			'cartCredit' => $this->_credit(),
			'cartPromo' => $this->_promocode()
		);
		$items = array(
			$this->_item()
		);
		$avatax = array(
			'tax' => 0
		);
		$result = OrderMock::recordOrder(
			$vars,
			$items,
			$this->_card(false),
			$order,
			$avatax,
			$authKey,
			$items
		);
		$this->assertTrue($result);

		$expected = 10;
		$result = $order->discount;
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testRecordOrderCartCreditAmountPositive() {
		$authKey = '090909099909';
		$address = $this->_address();

		$this->user->save(
			array('total_credit' => 2),
			array('validate' => false)
		);

		$data = array(
			'authKey' => $authKey,
			'handling' => 7.95,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'subTotal' => 56.7,
			'tax' => 0,
			'total' => 49.65,
			'discount' => 9,
			'user_id' => $this->user->_id
		);
		$order = OrderMock::create($data);
		$result = $order->save();
		$this->assertTrue($result);

		$credit = $this->_credit();
		$credit->credit_amount = 3.45;

		$vars = array(
			'creditCard' => $this->_card(true),
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 1,
			'cartCredit' => $credit,
			'cartPromo' => $this->_promocode()
		);
		$items = array(
			$this->_item()
		);
		$avatax = array(
			'tax' => 0
		);

		$expected = 0;
		$result = $order->credit_used;
		$this->assertEqual($expected, $result);

		$expected = 2;
		$result = User::first((string) $this->user->_id)->total_credit;
		$this->assertEqual($expected, $result);

		Session::write('credit', 500);

		OrderMock::recordOrder(
			$vars,
			$items,
			$this->_card(false),
			$order,
			$avatax,
			$authKey,
			$items
		);

		$expected = 3.45;
		$result = $order->credit_used;
		$this->assertEqual($expected, $result);

		$result = Session::check('credit');
		$this->assertFalse($result);

		$expected = 5.45;
		$result = User::first((string) $this->user->_id)->total_credit;
		$this->assertEqual($expected, $result);

		$credit = Credit::first(array(
			'conditions' => array('user_id' => (string) $this->user->_id)
		));
		$expected = 3.45;
		$result = $credit->credit_amount;
		$this->assertEqual($expected, $result);

		$credit->delete();
		$order->delete();
	}

	public function testCreditCardCryptSymmetry() {
		$this->skipIf(!extension_loaded('mcrypt'), 'No mcrypt extension.');

		$creditCard = array(
			 'number' => '4111111111111111',
			 'month' => 11,
			 'year' => 2023
		);

		$result = Order::creditCardEncrypt($creditCard, $this->user->_id, true);
		$this->assertTrue($result);

		Session::write('cc_infos', $result);

		$expected = $creditCard;
		$result = Order::creditCardDecrypt($this->user->_id);
		$this->assertEqual($expected, $result);
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

	protected function _card($raw = false) {
		$creditCard = array(
			 'number' => '4111111111111111',
			 'month' => 11,
			 'year' => 2023,
			 'type' => 'visa'
		);
		if ($raw) {
			return $creditCard;
		}
		$billing = ProcessorMock::create('default', 'address', $this->_address());
		return ProcessorMock::create('default', 'creditCard', $creditCard + compact('billing'));
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

	protected function _promocode() {
		$data = array(
			'code' => 'whattoexpect',
			'saved_amount' => -3.3
		);
		$promocode = Promocode::create();
		$promocode->save($data, array('validate' => false));

		$promocode->code_id = $promocode->_id;
		$promocode->save();

		return $this->_delete[] = $promocode;
	}

	protected function _credit() {
		$data = array(
			'credit' => 0,
			'credit_amount' => 0,
			'order_number' => '4D03KLKLLKL8FE3',
			'reason' => 'Credit Adjustment',
			'description' =>  'Credit Returned to user.',
		);
		$credit = Credit::create();
		$credit->save($data, array('validate' => false));

		return $this->_delete[] = $credit;
	}
}

?>