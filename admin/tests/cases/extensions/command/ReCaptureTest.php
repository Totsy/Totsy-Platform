<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\ReCapture;
use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoId;
use li3_payments\payments\Processor;
use li3_payments\extensions\adapter\payment\CyberSource;

class ReCaptureTest extends \lithium\test\Unit {
	
	protected $_Amexcustomer = null;
	
	protected $_AmexCustomerId = null;
	
	protected $_AmexCard = null;
	
	protected $_Visacustomer = null;
	
	protected $_VisaCustomerId = null;
	
	protected $_VisaCard = null;
	
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
		
		$this->_Visacustomer = Processor::create('test', 'customer', array(
			'firstName' => 'TomTest',
			'lastName' => 'DevTest',
			'email' => 'devtest@totsy.com',
			'payment' => Processor::create('test', 'creditCard', $this->_VisaCard),
			'billing' => Processor::create('test', 'address', array(
				'firstName' => 'Tomfsfdsd',
				'lastName' => 'Royerdfsfsdf',
				'address' => '100 test street',
				'address2' => 'APT1',
				'city' => 'Brooklyn',
				'state' => 'NY',
				'zip' => '11211',
				'country' => 'US',
				'email' => 'devtest@totsy.com'
			))
		));
		
		$resultVisa = $this->_Visacustomer->save();
		
		$this->_VisaCustomerId = $resultVisa->response->paySubscriptionCreateReply->subscriptionID;
		
		$this->_Amexcustomer = Processor::create('test', 'customer', array(
			'firstName' => 'TomTest',
			'lastName' => 'DevTest',
			'email' => 'devtest@totsy.com',
			'payment' => Processor::create('test', 'creditCard', $this->_AmexCard),
			'billing' => Processor::create('test', 'address', array(
				'firstName' => 'Tomfsfdsd',
				'lastName' => 'Royerdfsfsdf',
				'address' => '100 test street',
				'address2' => 'APT1',
				'city' => 'Brooklyn',
				'state' => 'NY',
				'zip' => '11211',
				'country' => 'US',
				'email' => 'devtest@totsy.com'
			))
		));
		
		$resultAmex = $this->_Amexcustomer->save();
		
		$this->_AmexCustomerId = $resultAmex->response->paySubscriptionCreateReply->subscriptionID;
	}
	
	public function testCaptureWithNewAuth() {
		$ordersCollection = Order::Collection();
		$cybersource = new CyberSource(Processor::config('default'));
		$profile = $cybersource->profile($this->_AmexCustomerId);
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('default', 100, $profile);
		$this->assertTrue($authorizeObject->success());
		$captureObject = Processor::capture('default', $authorizeObject, 100,
				array('processor' => $authorizeObject->adapter
		));
		$this->assertTrue($captureObject->success());
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'amex',
				'card_number' => '0005',
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'cyberSourceProfileId' => $this->_AmexCustomerId,
				'authTotal' => 100.00,
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
		$ReCapture = new ReCapture();
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
	
	/**public function testCaptureWithOldAuth() {
		$ordersCollection = Order::Collection();
		$cybersource = new CyberSource(Processor::config('default'));
		$profile = $cybersource->profile($this->_AmexCustomerProfileId);
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('default', 100, $profile);
		$this->assertTrue($authorizeObject->success());
		$captureObject = Processor::capture('default', $authorizeObject, 100,
				array('processor' => $authorizeObject->adapter
		));
		$this->assertTrue($captureObject->success());
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		#Temporary Order Creation
		$order = Order::create(array('_id' => new MongoId()));
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$order->cyberSourceProfileId = $this->_AmexCustomerProfileId;
		$order->save(array(
				'total' => 100.00,
				'card_type' => 'amex',
				'card_number' => '0005',
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'authTotal' => 100.00,
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
		$ReCapture = new ReCapture();
		$ReCapture->ordersIdFile = $this->fileTestName;
		$ReCapture->newAuth = false;
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
	}**/
	

}