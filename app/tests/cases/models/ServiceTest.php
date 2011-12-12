<?php

namespace app\tests\cases\models;

use app\models\User;
use MongoId;
use MongoDate;
use app\models\Service;
use lithium\storage\Session;
use app\tests\mocks\storage\session\adapter\MemoryMock;

class ServiceTest extends \lithium\test\Unit {

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

		Session::config(array(
			'default' => array('adapter' => new MemoryMock())
		));
	}

	public function tearDown() {
		foreach ($this->_delete as $document) {
			$document->delete();
		}
	}

	public function testFreeShippingCheck() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));
		Session::write('services', array(
			'freeshipping' => 'eligible'
		));

		$expected = array(
			'shippingCost' => 7.95,
			'overSizeHandling' => 0,
			'enable' => true
		);
		$result = Service::freeshippingCheck();
		$this->assertEqual($expected, $result);

		$expected = array(
			'items' => 32,
			'discount' => 0,
			'services' => 7.95
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
		Session::delete('services');
	}

	public function testFreeShippingCheckNotEligible() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));
		Session::write('services', array(
			'freeshipping' => 'XXX'
		));

		$expected = array(
			'shippingCost' => 0,
			'overSizeHandling' => 0,
			'enable' => false
		);
		$result = Service::freeshippingCheck();
		$this->assertEqual($expected, $result);

		$expected = array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
		Session::delete('services');
	}

	public function testFreeShippingCheckNotEligibleNoKey() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));
		Session::write('services', array());

		$expected = array(
			'shippingCost' => 7.95,
			'overSizeHandling' => 0,
			'enable' => false
		);
		$result = Service::freeshippingCheck();
		$this->assertEqual($expected, $result);

		$expected = array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
		Session::delete('services');
	}

	public function testTenOffFiftyCheck() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));
		Session::write('services', array(
			'10off50' => 'eligible'
		));

		$expected = 10;
		$result = Service::tenOffFiftyCheck(100);
		$this->assertEqual($expected, $result);

		$expected = array(
			'items' => 32,
			'discount' => 0,
			'services' => 10
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
		Session::delete('services');
	}

	public function testTenOffFiftyCheckNotEligible() {
		Session::write('userLogin', $this->user->data());
		Session::write('userSavings', array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		));
		Session::write('services', array(
			'10off50' => 'XXX'
		));

		$expected = 0;
		$result = Service::tenOffFiftyCheck(100);
		$this->assertEqual($expected, $result);

		$expected = array(
			'items' => 32,
			'discount' => 0,
			'services' => 0
		);
		$result = Session::read('userSavings');
		$this->assertEqual($expected, $result);

		Session::delete('userLogin');
		Session::delete('userSavings');
		Session::delete('services');
	}
}

?>
