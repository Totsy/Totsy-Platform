<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\ReAuthorize;
use admin\extensions\command\VoidTransaction;
use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoId;
use li3_payments\payments\Processor;

class VoidTransactionTest extends \lithium\test\Unit {
	
	protected $_Amexcustomer = null;
	
	protected $_AmexCard = null;
	
	protected $_billingAddress = null;
	
	protected $_ReAuthLimitDate = 8;
	
	protected $_FullReAuthLimitDate = 1;
	
	protected $_VoidLimitDate = 7;
	
	public function setUp() {
		$this->_UserInfos = array(
			'firstname' => 'Tomfsfdsd',
			'lastname' => 'Royerdfsfsdf',
			'email' => 'gsdgfdfgdsfg@sdfsdfsd.com'
		);
		$this->_VisaCard = array( 
			'type' => 'visa',
			'number' => '4111111111111111',
			'month' => 5,
			'year' => 2015,
			'code' => 122 
		);
		$this->_billingAddress = array(
				'firstname' => 'Tomfsfdsd',
				'lastname' => 'Royerdfsfsdf',
				'address' => '143 roebling street',
				'city' => 'Brooklyn',
				'state' => 'NY',
				'zip' => '11211',
				'country' => 'US',
				'email' => 'gsdgfdfgdsfg@sdfsdfsd.com'
		);
		
		$this->_Visacustomer = Processor::create('test', 'creditCard', array(
			'type' => 'visa',
			'number' => '4111111111111111',
			'month' => 4,
			'year' => 2014,
			'code' => 123,
			'billing' => Processor::create('test', 'address', array(
				'firstName' => 'Tomfsfdsd',
				'lastName' => 'Royerdfsfsdf',
				'address' => '143 roebling street',
				'city' => 'Brooklyn',
				'state' => 'NY',
				'zip' => '11211',
				'country' => 'US',
				'email' => 'gsdgfdfgdsfg@sdfsdfsd.com'
			))
		));
	}
	
	public function testVoidwithNotFullAuth() {
		$ordersCollection = Order::Collection();
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 1, $this->_Visacustomer);
		$this->assertTrue($authorizeObject->success());		
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_VisaCard, (string) $user->_id);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_VoidLimitDate, date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'visa',
				'card_number' => '1111',
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'authTotal' => 1.00,
				'cc_payment' => $cc_encrypt,
				'user_id' => (string) $user->_id,
				'billing' => $this->_billingAddress,
				'test' => true
		));
		#Running Li3 command Reauthorize
		$orders = $ordersCollection->find(array(
			'test' => true,
			'cancel' => array('$ne' => true)
		));
		$VoidTransaction = new VoidTransaction();
		$VoidTransaction->unitTest = true;
		$VoidTransaction->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertTrue(!array_key_exists('void_records', $order));
		$this->assertTrue(!array_key_exists('void_records', $order_test));
		$this->assertEqual(true , $order_test['authKey'] == $order->authKey);
		$this->assertEqual(false , $order_test['authTotal'] != 1.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
	
	public function testVoidwithFullAuth() {
		$ordersCollection = Order::Collection();
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 1, $this->_Visacustomer);
		$this->assertTrue($authorizeObject->success());		
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_VisaCard, (string) $user->_id);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_VoidLimitDate, date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'visa',
				'card_number' => '1111',
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'authTotal' => 100.00,
				'cc_payment' => $cc_encrypt,
				'user_id' => (string) $user->_id,
				'billing' => $this->_billingAddress,
				'test' => true
		));
		#Running Li3 command Reauthorize
		$orders = $ordersCollection->find(array(
			'test' => true,
			'cancel' => array('$ne' => true)
		));
		$VoidTransaction = new VoidTransaction();
		$VoidTransaction->unitTest = true;
		$VoidTransaction->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertTrue(!array_key_exists('void_records', $order));
		$this->assertTrue(array_key_exists('void_records', $order_test));
		$this->assertEqual(false, $order_test['authKey'] == $order->authKey);
		$this->assertEqual(true, $order_test['authTotal'] == 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
	
	public function testVoidwithFullAuthTooEarly() {
		$ordersCollection = Order::Collection();
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 1, $this->_Visacustomer);
		$this->assertTrue($authorizeObject->success());		
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_VisaCard, (string) $user->_id);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(date("H"), date("i"), date("s"), date("m"), date("d") - ($this->_VoidLimitDate - 1), date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'visa',
				'card_number' => '1111',
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'authTotal' => 100.00,
				'cc_payment' => $cc_encrypt,
				'user_id' => (string) $user->_id,
				'billing' => $this->_billingAddress,
				'test' => true
		));
		#Running Li3 command Reauthorize
		$orders = $ordersCollection->find(array(
			'test' => true,
			'cancel' => array('$ne' => true)
		));
		$VoidTransaction = new VoidTransaction();
		$VoidTransaction->unitTest = true;
		$VoidTransaction->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertTrue(!array_key_exists('void_records', $order));
		$this->assertTrue(!array_key_exists('void_records', $order_test));
		$this->assertEqual(true, $order_test['authKey'] == $order->authKey);
		$this->assertEqual(true, $order_test['authTotal'] == 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
}