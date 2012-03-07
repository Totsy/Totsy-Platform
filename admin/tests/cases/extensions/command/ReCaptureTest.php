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
	
	protected $_AmexCustomer = null;
	
	protected $_AmexCustomerId = null;
	
	protected $_AmexCard = null;
	
	protected $_VisaCustomer = null;
	
	protected $_VisaCustomerId = null;
	
	protected $_VisaCard = null;
	
	protected $_MasterCardCustomer = null;
	
	protected $_MasterCardCustomerId = null;
	
	protected $_MasterCard = null;
	
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
		$this->_MasterCard = array( 
			'type' => 'mc',
			'number' => '5555555555554444',
			'month' => 2,
			'year' => 2016,
			'code' => 177 
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
		
		$this->_VisaCustomer = Processor::create('test', 'customer', array(
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
		
		$resultVisa = $this->_VisaCustomer->save();
		
		$this->_VisaCustomerId = $resultVisa->response->paySubscriptionCreateReply->subscriptionID;
		
		$this->_AmexCustomer = Processor::create('test', 'customer', array(
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
		
		$resultAmex = $this->_AmexCustomer->save();
		
		$this->_AmexCustomerId = $resultAmex->response->paySubscriptionCreateReply->subscriptionID;
	
		$this->_MasterCardCustomer = Processor::create('test', 'customer', array(
			'firstName' => 'TomTest',
			'lastName' => 'DevTest',
			'email' => 'devtest@totsy.com',
			'payment' => Processor::create('test', 'creditCard', $this->_MasterCard),
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
		
		$resultMasterCard = $this->_MasterCardCustomer->save();
		
		$this->_MasterCardCustomerId = $resultMasterCard->response->paySubscriptionCreateReply->subscriptionID;
	}
	
	public function testReCaptureFunctionality() {
		#Test Recapture by Creating New Authorization with Amex
		$this->reCapture($this->_AmexCustomerId, 'amex', '0005', false);
		#Test Recapture by Creating New Authorization with Visa
		$this->reCapture($this->_VisaCustomerId, 'visa', '1111', false);
		#Test Recapture by Creating New Authorization with MasterCard
		$this->reCapture($this->_MasterCardCustomerId, 'mc', '4444', false);
		#Test Recapture but Only Authorization with Amex
		$this->reCapture($this->_AmexCustomerId, 'amex', '0005', true);
		#Test Recapture by Keeping Same Authorization with Visa
		$this->reCapture($this->_VisaCustomerId, 'visa', '1111', true);
		#Test Recapture by Keeping Same Authorization with MasterCard
		$this->reCapture($this->_MasterCardCustomerId, 'mc', '4444', true);
	}
	
	public function reCapture($customerId, $type, $card_number, $onlyReauthorization) {
		$ordersCollection = Order::Collection();
		#Create Temporary order
		$order = Order::create(array('_id' => new MongoId()));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$cybersource = new CyberSource(Processor::config('test'));
		$profile = $cybersource->profile($customerId);
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', 100, $profile, array('orderID' => $order->order_id));
		$this->assertTrue($authorizeObject->success());
		$captureObject = Processor::capture('test', $authorizeObject, 100,
				array('processor' => $authorizeObject->adapter, 'orderID' => $order['order_id']
		));
		$this->assertTrue($captureObject->success());
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d"), date("Y")));	
		$order->save(array(
				'total' => 100.00,
				'card_type' => $type,
				'card_number' => $card_number,
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'cyberSourceProfileId' => $customerId,
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
		$ReCapture->onlyReauth = $onlyReauthorization;
		$ReCapture->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual($onlyReauthorization, empty($order_test['payment_captured']));
		$this->assertEqual(true, $order_test['authKey'] != $order->authKey);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
		unlink($myFilePath);
	}
	
}