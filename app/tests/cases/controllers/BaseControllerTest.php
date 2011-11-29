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

	protected function _fixture($short, $class) {
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
		$expected = 0;
		$result = count($controller->classes());
		$this->assertNotEqual($expected, $result);
	}

	public function testInitWithoutUser() {
		$request = new Request(array('params' => array('controller' => 'base', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$set = $controller->renderData();

		$result = array_key_exists('userInfo', $set);
		$message = $set;
		$this->assertTrue($result, $message);

		$result = $set['userInfo'];
		$this->assertNull($result);

		$result = isset($set['cartCount']);
		$message = $set;
		$this->assertTrue($result, $message);
	}

	public function testInitWithUser() {
		$user = $this->_fixture('user1', 'User');
		Session::write('userLogin', $user->data());
		$request = new Request(array('params' => array('controller' => 'base', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$set = $controller->renderData();

		$result = isset($set['userInfo']);
		$message = $set;
		$this->assertTrue($result, $message);

		$expected = $user->_id;
		$result = $set['userInfo']['_id'];
		$this->assertEqual($expected, $result);

		$result = isset($set['credit']);
		$message = $set;
		$this->assertTrue($result, $message);

		$expected = number_format($user->credit, 0);
		$result = $set['credit'];
		$this->assertEqual($expected, $result);
	}

	protected function _checkEligible($method, $key, $expected, $user) {
		Session::write('services', array());
		$controller = new BaseControllerMock();
		$controller->invokeMethod($method, array(array('_id' => $user->_id)));
		$session = Session::read('services');

		$result = isset($session[$key]);
		$message = $session;
		$this->assertTrue($result, $message);

		$result = $session[$key];
		$this->assertEqual($expected, $result);
	}

	protected function _checkFreeShippingEligible($expected, $attributes) {
		$user = $this->_fixture('user1', 'User');
		$user->save($attributes, array('validate' => false));
		$this->_checkEligible('freeShippingEligible', 'freeshipping', $expected, $user);
	}

	public function testFreeShippingEligible() {
		$this->_checkFreeShippingEligible('eligible', array('created_date' => new MongoDate()));
	}

	public function testFreeShippingEligibleUsed() {
		$this->_checkFreeShippingEligible('used', array('created_date' => new MongoDate(), 'purchase_count' => 1));
	}

	public function testFreeShippingEligibleExpired() {
		$this->_checkFreeShippingEligible('expired', array('created_date' => new MongoDate(time() - 60 * 24 * 60 * 60)));
	}

	protected function _checkTenOffFiftyEligible($expected, $attributes, $order_attributes = null) {
		$user = $this->_fixture('user1', 'User');
		$user->save($attributes, array('validate' => false));
		if ($order_attributes) {
			$order = Order::create($order_attributes + array('user_id' => $user->_id));
			$order->save(null, array('validate' => false));
		}
		$this->_checkEligible('tenOffFiftyEligible', '10off50', $expected, $user);
		if ($order_attributes) {
			$order->delete();
		}
	}

	public function testTenOffFiftyEligible() {
		$this->_checkTenOffFiftyEligible(
			'eligible',
			array('created_date' => new MongoDate(), 'purchase_count' => 1),
			array('date_created' => new MongoDate())
		);
	}

	public function testTenOffFiftyEligibleIneligible() {
		$this->_checkTenOffFiftyEligible('ineligible', array('created_date' => new MongoDate(), 'purchase_count' => 0));
	}

	public function testTenOffFiftyEligibleExpired() {
		$this->_checkTenOffFiftyEligible(
			'expired',
			array('created_date' => new MongoDate(time() - 60 * 24 * 60 * 60), 'purchase_count' => 1),
			array('date_created' => new MongoDate())
		);
	}

	public function testWriteSession() {
		$controller = new BaseControllerMock();
		$controller->writeSession(array('foo' => 'bar'));

		$expected = array('foo' => 'bar');
		$result = Session::read('userLogin');
		$this->assertEqual($expected, $result);
	}

	public function testCleanCC() {
		Session::write('cc_infos', array('foo' => 'bar'));
		$request = new Request(array('params' => array('controller' => 'base', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$controller->cleanCC();

		$expected = array();
		$result = Session::read('cc_infos');
		$this->assertEqual($expected, $result);
	}

	public function testCleanCCDoesNotClean() {
		Session::write('cc_infos', array('foo' => 'bar'));
		$request = new Request(array('params' => array('controller' => 'orders', 'action' => 'index')));
		$controller = new BaseControllerMock(compact('request'));
		$controller->cleanCC();

		$expected = array('foo' => 'bar');
		$result = Session::read('cc_infos');
		$this->assertEqual($expected, $result);
	}
}

?>