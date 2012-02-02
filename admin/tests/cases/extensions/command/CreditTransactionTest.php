<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\CreditTransaction;
use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoId;
use li3_payments\payments\Processor;

class CreditTransactionTest extends \lithium\test\Unit {
	
	protected $_billingAddress = null;
	
	protected $fileTestName = "credit_test.csv";
	
	protected $folderTestName = "/resources/totsy/tmp/";
	
	protected $_amountOfTransaction = 100;
	
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
		
		$this->_MasterCardcustomer = Processor::create('test', 'creditCard', array(
			'type' => 'mc',
			'number' => '5555555555554444',
			'month' => 2,
			'year' => 2016,
			'code' => 177,
			'billing' => Processor::create('test', 'address', array(
				'firstName' => 'TestFirstName',
				'lastName' => 'TestLastName',
				'address' => '144 roebling street',
				'city' => 'Brooklyn',
				'state' => 'NY',
				'zip' => '12211',
				'country' => 'US',
				'email' => 'test@totsy.com'
			))
		));
	}
	
	public function testCreditTransactions() {
		#Test Credit Transaction with Amex
		$this->CreditOneTransaction($this->_Amexcustomer, $this->_AmexCard, 'amex', '0005');
		#Test Credit Transaction with Visa
		$this->CreditOneTransaction($this->_Visacustomer, $this->_VisaCard, 'visa', '1111');
		#Test Credit Transaction with MasterCard
		$this->CreditOneTransaction($this->_MasterCardcustomer, $this->_MasterCard, 'mc', '4444');
	}
	
	public function CreditOneTransaction($customer, $card, $type, $card_number) {
		$ordersCollection = Order::Collection();
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', $this->_amountOfTransaction, $customer);
		$this->assertTrue($authorizeObject->success());
		$captureObject = Processor::capture('test', $authorizeObject, $this->_amountOfTransaction,
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
				'total' => $this->_amountOfTransaction,
				'card_type' => $type,
				'card_number' => $card_number,
				'authKey' => $captureObject->key,
				'auth' => $captureObject->export(),
				'processor' => $captureObject->adapter,
				'authTotal' => $this->_amountOfTransaction,
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
		#Running Li3 command CreditTransaction
		$CreditTransaction = new CreditTransaction();
		$CreditTransaction->ordersIdFile = $this->fileTestName;
		$CreditTransaction->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		$this->assertEqual( false , empty($order_test['credited']));
		$this->assertEqual( false , $order_test['authKey'] == $order->credit_authKey);
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
		unlink($myFilePath);
	}

}