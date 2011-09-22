<?php

namespace app\tests\cases\models;

use app\tests\mocks\models\OrderMock;
use MongoId;
use lithium\storage\Session;
use app\models\User;
use app\models\Credit;
use app\models\Promocode;
use app\models\Item;

class OrderTest extends \lithium\test\Unit {

	public $user;

	protected $_backup = array();

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

		$session = $this->user->data();
		Session::write('userLogin', $session);
	}

	public function tearDown() {
		Session::delete('userLogin');

		$this->user->delete();
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
		$data = array(
			'amount' => 3.25,
			'order_number' => '4D03KLKLLKL8FE3',
			'reason' => 'Credit Adjustment',
			'description' =>  'Credit Returned to user.',
		);
		$credit = Credit::create();
		$credit->save($data, array('validate' => false));

		$data = array(
			'code' => 'whattoexpect',
			'saved_amount' => -3.3
		);
		$promocode = Promocode::create();
		$promocode->save($data, array('validate' => false));

		$promocode->code_id = $promocode->_id;
		$promocode->save();

		$data =  array(
			'category' => 'Baby Gear',
			'color' => '',
			'description' => 'BabyGanics Alcohol Free Hand Sanitizer 250ml',
			'discount_exempt' => false,
			'expires' => array(
				'sec' => 1292079402,
				'usec' => 0
			),
			'primary_image' => '4d015488ce64e5c072fc1e00',
			'product_weight' => 0.64,
			'quantity' => 5,
			'sale_retail' => 3,
			'size' => 'no size',
			'url' => 'babyganics-alcohol-free-hand-sanitizer-250ml',
			'event_name' => 'Babyganics',
			'event_id' => '4cfd1dd1ce64e5300aeb4100',
			'line_number' => 0,
			'status' => 'Order Placed'
		);
		$item = Item::create();
		$item->save($data, array('validate' => false));

		$address = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'address' => 'Hollywood Blvd ' . uniqid(),
			'address2' => 'c/o Skywalker',
			'city' => 'Lost Angeles',
			'zip' => '90001',
			'state' => 'California',
			'country' => 'USA'
		);
		$creditCard = array(
			 'number' => '4111111111111111',
			 'month' => 11,
			 'year' => 2023
		);

		$data = array();
		$cart = array(
			$item
		);
		$vars = array(
			'creditCard' => $creditCard,
			'billingAddr' => $address,
			'shippingAddr' => $address,
			'total' => 123.45,
			'subTotal' => 123.45,
			'shippingCost' => 10,
			'overShippingCost' => 0,
			'cartCredit' => $credit,
			'cartPromo' => $promocode
		);
		$avatax = array(
			'tax' => 0
		);

		$result = OrderMock::process($data, $cart, $vars, $avatax);
		$this->assertTrue($result);

		$result = Session::read('cc_error');
		$this->assertFalse($result);

		$credit->delete();
		$promocode->delete();
		$item->delete();
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
}

?>