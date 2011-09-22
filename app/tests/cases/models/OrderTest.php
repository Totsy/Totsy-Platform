<?php

namespace app\tests\cases\models;

use app\tests\mocks\models\OrderMock;
use app\tests\mocks\extensions\PaymentsMock;
use MongoId;
use lithium\storage\Session;
use app\models\User;
use app\models\Credit;
use app\models\Promocode;
use app\models\Item;

class OrderTest extends \lithium\test\Unit {

	public $user;

	protected $_backup = array();

	protected $_delete = array();

	public function setUp() {
		Session::config(array(
			'default' => array('adapter' => 'Memory')
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

		$result = OrderMock::process($data, $items, $vars, $avatax);
		$this->assertTrue($result);

		$result = Session::read('cc_error');
		$this->assertFalse($result);

		$expected = 123.45;
		$result = PaymentsMock::$authorize[1];
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['number'];
		$result = PaymentsMock::$authorize[2]->number;
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['month'];
		$result = PaymentsMock::$authorize[2]->month;
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['year'];
		$result = PaymentsMock::$authorize[2]->year;
		$this->assertEqual($expected, $result);
	}


	public function testRecordOrderWithoutService() {
		$authKey = '090909099909';
		$address = $this->_address();

		$data = array(
			'authKey' => $authKey,
			'credit_used' => -5,
			'date_created' => 'Sat, 11 Dec 2010 09: 51: 15 -0500',
			'handling' => 7.95,
			'order_id' => '4D03KLKLLKL8FE3',
			'promo_code' => 'weekend10',
			'promo_discount' => -10,
			'ship_date' => 1294272000,
			'shipping' => array(
				'description' => 'Home',
			) + $address,
			'shippingMethod' => 'ups',
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
			$this->_card(),
			$order,
			$avatax,
			$authKey,
			$items
		);
		$this->assertTrue($result);

		$result = Session::read('cc_error');
		$this->assertFalse($result);

		$expected = 123.45;
		$result = PaymentsMock::$authorize[1];
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['number'];
		$result = PaymentsMock::$authorize[2]->number;
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['month'];
		$result = PaymentsMock::$authorize[2]->month;
		$this->assertEqual($expected, $result);

		$expected = $vars['creditCard']['year'];
		$result = PaymentsMock::$authorize[2]->year;
		$this->assertEqual($expected, $result);

		$order->delete();
	}

	public function testCreditCardCryptSymmetry() {
		$this->skipIf(!extension_loaded('mcrypt'), 'No mcrypt extension.');

		$creditCard = array(
			 'number' => '4111111111111111',
			 'month' => 11,
			 'year' => 2023
		);
		Session::write('cc_infos', $creditCard);

		$result = OrderMock::creditCardEncrypt($this->user->_id);
		$this->assertTrue($result);

		$expected = $creditCard;
		$result = OrderMock::creditCardDecrypt($this->user->_id);
		$this->assertEqual($expected, $result);
	}

	protected function _item() {
		$data = array(
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
		$item->item_id = $item->_id;
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
			'amount' => 3.25,
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