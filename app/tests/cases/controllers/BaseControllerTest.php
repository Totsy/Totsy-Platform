<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\tests\mocks\controllers\BaseControllerMock;
use lithium\storage\Session;
use app\models\User;
use app\models\Order;
use li3_fixtures\test\Fixture;
use lithium\core\Libraries;
use MongoDate;

class BaseControllerTest extends \lithium\test\Unit {
	protected $fixtures = array();
	protected $delete = array();

	public function setUp() {
		$this->sessionConfig = Session::Config();
		Session::config(array(
			'default' => array('adapter' => 'app\tests\mocks\storage\session\adapter\MemoryMock'),
			'cookie' => array('adapter' => 'app\tests\mocks\storage\session\adapter\MemoryMock')
		));
	}

	public function tearDown() {
		foreach ($this->delete as $doc) {
			$doc->delete();
		}
		Session::Config($this->sessionConfig);
	}

	protected function fixture($short, $class) {
		if (!isset($this->fixtures[$class])) {
			$this->fixtures[$class] = Fixture::load($class);
		}
		$klass = Libraries::locate('models', $class);
		$doc = $klass::create($this->fixtures[$class][$short]);
		$doc->save();
		$this->delete[] = $doc;
		return $doc;
	}

	public function testConstructAddsClasses() {
		$controller = new BaseControllerMock();
		$this->assertNotEqual(0, count($controller->classes()));
	}

	public function testInitWithoutUser() {
		$request = new Request(array('params' => array('controller' => 'base', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$set = $controller->renderData();
		$this->assertTrue(array_key_exists('userInfo', $set), $set);
		$this->assertNull($set['userInfo']);
		$this->assertTrue(isset($set['cartCount']), $set);
	}

	public function testInitWithUser() {
		$user = $this->fixture('user1', 'User');
		Session::write('userLogin', $user->data());
		$request = new Request(array('params' => array('controller' => 'base', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$set = $controller->renderData();
		$this->assertTrue(isset($set['userInfo']), $set);
		$this->assertEqual($set['userInfo']['_id'], $user->_id);
		$this->assertTrue(isset($set['credit']), $set);
		$this->assertEqual($set['credit'], number_format($user->credit, 0));
	}

	protected function checkEligible($method, $key, $expected, $user) {
		Session::write('services', array());
		$controller = new BaseControllerMock();
		$controller->invokeMethod($method, array(array('_id' => $user->_id)));
		$result = Session::read('services');
		$this->assertTrue(isset($result[$key]), $result);
		$this->assertEqual($expected, $result[$key]);
	}

	protected function checkFreeShippingEligible($expected, $attributes) {
		$user = $this->fixture('user1', 'User');
		$user->save($attributes, array('validate' => false));
		$this->checkEligible('freeShippingEligible', 'freeshipping', $expected, $user);
	}

	public function testFreeShippingEligible() {
		$this->checkFreeShippingEligible('eligible', array('created_date' => new MongoDate()));
	}

	public function testFreeShippingEligibleUsed() {
		$this->checkFreeShippingEligible('used', array('created_date' => new MongoDate(), 'purchase_count' => 1));
	}

	public function testFreeShippingEligibleExpired() {
		$this->checkFreeShippingEligible('expired', array('created_date' => new MongoDate(time() - 60 * 24 * 60 * 60)));
	}

	protected function checkTenOffFiftyEligible($expected, $attributes, $order_attributes = null) {
		$user = $this->fixture('user1', 'User');
		$user->save($attributes, array('validate' => false));
		if ($order_attributes) {
			$order = Order::create($order_attributes + array('user_id' => $user->_id));
			$order->save(null, array('validate' => false));
		}
		$this->checkEligible('tenOffFiftyEligible', '10off50', $expected, $user);
		if ($order_attributes) {
			$order->delete();
		}
	}

	public function testTenOffFiftyEligible() {
		$this->checkTenOffFiftyEligible(
			'eligible',
			array('created_date' => new MongoDate(), 'purchase_count' => 1),
			array('date_created' => new MongoDate())
		);
	}

	public function testTenOffFiftyEligibleIneligible() {
		$this->checkTenOffFiftyEligible('ineligible', array('created_date' => new MongoDate(), 'purchase_count' => 0));
	}

	public function testTenOffFiftyEligibleExpired() {
		$this->checkTenOffFiftyEligible(
			'expired',
			array('created_date' => new MongoDate(time() - 60 * 24 * 60 * 60), 'purchase_count' => 1),
			array('date_created' => new MongoDate())
		);
	}

	public function testWriteSession() {
		$controller = new BaseControllerMock();
		$controller->writeSession(array('foo' => 'bar'));
		$result = Session::read('userLogin');
		$this->assertEqual(array('foo' => 'bar'), $result);
	}

	public function testCleanCC() {
		Session::write('cc_infos', array('foo' => 'bar'));
		$request = new Request(array('params' => array('controller' => 'base', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$controller->cleanCC();
		$result = Session::read('cc_infos');
		$this->assertEqual(array(), $result);
	}

	public function testCleanCCDoesNotClean() {
		Session::write('cc_infos', array('foo' => 'bar'));
		$request = new Request(array('params' => array('controller' => 'orders', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$controller->cleanCC();
		$result = Session::read('cc_infos');
		$this->assertEqual(array('foo' => 'bar'), $result);
	}
}

?>