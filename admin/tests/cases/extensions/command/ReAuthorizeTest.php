<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\ReAuthorize;
use admin\extensions\command\VoidTransaction;
use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoId;
use li3_payments\payments\Processor;

class ReAuthorizeTest extends \lithium\test\Unit {
	
	protected $_Amexcustomer = null;
	
	protected $_AmexCard = null;
	
	protected $_billingAddress = null;
	
	protected $_ReAuthLimitDate = 8;
	
	protected $_FullReAuthLimitDate = 1;
	
	public function setUp() {
		$this->_UserInfos = array(
			'firstname' => 'Tomfsfdsd',
			'lastname' => 'Royerdfsfsdf',
			'email' => 'gsdgfdfgdsfg@sdfsdfsd.com'
		);
		$this->_AmexCard = array( 
			'type' => 'amex',
			'number' => '378282246310005',
			'month' => 4,
			'year' => 2014,
			'code' => 123 
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
		
		$this->_Amexcustomer = Processor::create('test', 'creditCard', array(
			'type' => 'amex',
			'number' => '378282246310005',
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

	public function testfullReAuthorizeAmex() {
		$ordersCollection = Order::Collection();
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 1, $this->_Amexcustomer);
		$this->assertTrue($authorizeObject->success());		
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_AmexCard, (string) $user->_id);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_FullReAuthLimitDate, date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'amex',
				'card_number' => '0005',
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
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->fullAmount = true;
		$ReAuthorize->orders = $orders;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false , $order_test['authKey'] == $order->authKey);
		$this->assertEqual(false , $order_test['authTotal'] != 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}

	public function testfullReAuthorizeVisa() {
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
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_FullReAuthLimitDate, date("Y")));
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
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->fullAmount = true;
		$ReAuthorize->orders = $orders;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false , $order_test['authKey'] == $order->authKey);
		$this->assertEqual(false , $order_test['authTotal'] != 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}

	public function testReAuthorizeAmex1dollar() {
		$ordersCollection = Order::Collection();
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 1, $this->_Amexcustomer);
		$this->assertTrue($authorizeObject->success());		
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_AmexCard, (string) $user->_id);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'amex',
				'card_number' => '0005',
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
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false , $order_test['authKey'] != $order->authKey);
		$this->assertEqual(false , $order_test['authTotal'] != 1.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}

	public function testReAuthorizeAmexfullAmount() {
		$ordersCollection = Order::Collection();
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 1, $this->_Amexcustomer);
		$this->assertTrue($authorizeObject->success());		
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_AmexCard, (string) $user->_id);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'amex',
				'card_number' => '0005',
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
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false , $order_test['authKey'] == $order->authKey);
		$this->assertEqual(false , $order_test['authTotal'] != 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
	
	
	public function testReAuthorizeVisafullAmountwithProfile() {
		$ordersCollection = Order::Collection();
		#Create Temporary Profile on CyberSource
		$customer = Processor::create('default', 'customer', array(
			'firstName' => $this->_UserInfos['firstname'],
			'lastName' => $this->_UserInfos['lastname'],
			'email' => $this->_UserInfos['email'],
			'billing' => Processor::create('default', 'address', $this->_billingAddress),
			'payment' => Processor::create('default', 'creditCard', $this->_VisaCard)
		));
		$result = $customer->save();
		$this->assertTrue($result->success());
		$profileID = $result->response->paySubscriptionCreateReply->subscriptionID;
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
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
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
			'cyberSourceProfileId' => $profileID,
			'test' => true
		));		
		#Running Li3 command Void
		$VoidTransaction = new VoidTransaction();
		$VoidTransaction->unitTest = true;
		$VoidTransaction->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		$void_records = $order_test['void_records'];
		$void_records[0]['date_saved'] = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
		$update = $ordersCollection->update(
			array('_id' => $order->_id),
			array('$set' => array('void_records' => $void_records)), array( 'upsert' => true)
		);
		#Running Li3 command Reauthorize
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false , $order_test['authKey'] == $order->authKey);
		$this->assertEqual(false , $order_test['authTotal'] != 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
	
	public function testReAuthorizeVisa0dollar() {
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
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'visa',
				'card_number' => '1111',
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'authTotal' => 0.00,
				'cc_payment' => $cc_encrypt,
				'user_id' => (string) $user->_id,
				'billing' => $this->_billingAddress,
				'test' => true
		));
		#Running Li3 command Reauthorize
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false , $order_test['authKey'] != $order->authKey);
		$this->assertEqual(false , $order_test['authTotal'] != 0.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
	
	public function testReAuthorizeVisa() {
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
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
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
		#Running Li3 command Void
		$VoidTransaction = new VoidTransaction();
		$VoidTransaction->unitTest = true;
		$VoidTransaction->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		$void_records = $order_test['void_records'];
		$void_records[0]['date_saved'] = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - 1.1, date("Y")));
		$update = $ordersCollection->update(
			array('_id' => $order->_id),
			array('$set' => array('void_records' => $void_records)), array( 'upsert' => true)
		);
		#Running Li3 command Reauthorize
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test_auth = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(true , $order_test['authKey'] != $order_test_auth['authKey']);
		$this->assertEqual(true , $order_test_auth['authTotal'] == 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
	
	public function testReAuthorizeVisaWithTooEarlyVoid() {
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
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
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
		#Running Li3 command Void
		$VoidTransaction = new VoidTransaction();
		$VoidTransaction->unitTest = true;
		$VoidTransaction->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		$void_records = $order_test['void_records'];
		$void_records[0]['date_saved'] = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - 0.5, date("Y")));
		$update = $ordersCollection->update(
			array('_id' => $order->_id),
			array('$set' => array('void_records' => $void_records)), array( 'upsert' => true)
		);
		#Running Li3 command Reauthorize
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test_auth = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(true , $order_test['authKey'] == $order_test_auth['authKey']);
		$this->assertEqual(true , $order_test_auth['authTotal'] == 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
	
	public function testReAuthorizeVisaWithNoVoid() {
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
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
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
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false , $order_test['authKey'] != $order->authKey);
		$this->assertEqual(true , $order_test['authTotal'] == 100.00);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}

}