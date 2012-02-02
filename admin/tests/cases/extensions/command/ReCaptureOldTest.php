<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\ReCaptureOld;
use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoId;
use li3_payments\payments\Processor;

class ReCaptureOldTest extends \lithium\test\Unit {
	
	protected $_Amexcustomer = null;
	
	protected $_AmexCard = null;
	
	protected $_billingAddress = null;
	
	protected $fileTestName = "recapture_test.csv";
	
	protected $folderTestName = "/resources/totsy/tmp/";
	
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
			'month' => 4,
			'year' => 2014,
			'code' => 123 
		);
		$this->_billingAddress = array(
				'firstname' => 'Tomfsfdsd',
				'lastname' => 'Royerdfsfsdf',
				'address' => '143 roebling street',
				'address2' => 'apt1',
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
				'address2' => 'apt1',
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
				'address2' => 'apt1',
				'city' => 'Brooklyn',
				'state' => 'NY',
				'zip' => '11211',
				'country' => 'US',
				'email' => 'gsdgfdfgdsfg@sdfsdfsd.com'
			))
		));
	}
	
	public function testCaptureWithNewAuth() {
		$ordersCollection = Order::Collection();
		#Create Temporary order
		$order = Order::create(array('_id' => new MongoId()));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 100, $this->_Amexcustomer, array('orderID' => $order->order_id));
		$this->assertTrue($authorizeObject->success());
		$captureObject = Processor::capture('test', $authorizeObject, 100,
				array('processor' => $authorizeObject->adapter, 'orderID' => $order['order_id']
		));
		$this->assertTrue($captureObject->success());
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_AmexCard, (string) $user->_id);
		#Temporary Order Creation
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y")));
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
				'billing' => $this->_billingAddress
		));
		#Create File
		$myFilePath = LITHIUM_APP_PATH . $this->folderTestName . $this->fileTestName;
		$fh = fopen($myFilePath, 'wb');
		if(!empty($order)) {
			fputcsv($fh, array($order['order_id']));	
		}
		fclose($fh);
		#Running Li3 command Reauthorize
		$ReCapture = new ReCaptureOld();
		$ReCapture->ordersIdFile = $this->fileTestName;
		$ReCapture->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual( false , empty($order_test['payment_captured']));
		$this->assertEqual( false , $order_test['authKey'] == $order->authKey);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
		unlink($myFilePath);
	}
	
	public function testCaptureWithOldAuth() {
		$ordersCollection = Order::Collection();
		#Create Temporary order
		$order = Order::create(array('_id' => new MongoId()));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 100, $this->_Amexcustomer, array('orderID' => $order->order_id));
		$this->assertTrue($authorizeObject->success());
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Encrypt Specificied Credit Card
		$cc_encrypt = Order::creditCardEncrypt($this->_AmexCard, (string) $user->_id);
		#Temporary Order Creation
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y")));
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
				'billing' => $this->_billingAddress
		));
		#Create File
		$myFilePath = LITHIUM_APP_PATH . $this->folderTestName . $this->fileTestName;
		$fh = fopen($myFilePath, 'wb');
		if(!empty($order)) {
			fputcsv($fh, array($order['order_id']));	
		}
		fclose($fh);
		#Running Li3 command Reauthorize
		$ReCapture = new ReCaptureOld();
		$ReCapture->createNewAuth = false;
		$ReCapture->ordersIdFile = $this->fileTestName;
		$ReCapture->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual(false, empty($order_test['payment_captured']));
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
		unlink($myFilePath);
	}	

}