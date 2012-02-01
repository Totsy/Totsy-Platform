<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\ReAuthorize;
use admin\extensions\command\VoidTransaction;
use admin\models\Order;
use admin\models\User;
use MongoDate;
use MongoId;
use li3_payments\payments\Processor;
use li3_payments\extensions\adapter\payment\CyberSource;

class ReAuthorizeTest extends \lithium\test\Unit {
	
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
	
	public function testReAuthorizeFunctionality() {
		#Test Reauthorize processed during OrderExport with softAuth and Amex
		$this->reAuthorize($this->_AmexCustomerId, 'amex', '0005', 100.00, 1.00, true);
		#Test Reauthorize processed during OrderExport with softAuth and Visa
		$this->reAuthorize($this->_VisaCustomerId, 'visa', '1111', 100.00, 1.00, true);
		#Test Reauthorize processed during OrderExport with softAuth and MasterCard
		$this->reAuthorize($this->_MasterCardCustomerId, 'mc', '4444', 100.00, 1.00, true);
		#Test Reauthorize processed during CronJob with softAuth and Visa
		$this->reAuthorize($this->_VisaCustomerId, 'visa', '1111', 100.00, 1.00, false, true);
		#Test Reauthorize processed during CronJob with softAuth and MasterCard
		$this->reAuthorize($this->_MasterCardCustomerId, 'mc', '4444', 100.00, 1.00, false, true);
		#Test Reauthorize processed during CronJob with fullAuth and Visa
		$this->reAuthorize($this->_VisaCustomerId, 'visa', '1111', 100.00, 100.00, false, true);
		#Test Reauthorize processed during CronJob with fullAuth and MasterCard
		$this->reAuthorize($this->_MasterCardCustomerId, 'mc', '4444', 100.00, 100.00, false, true);
		#Test Reauthorize processed during CronJob with fullAuth and Visa + DELAY
		$this->reAuthorize($this->_VisaCustomerId, 'visa', '1111', 100.00, 100.00, false, true, 24);
		#Test Reauthorize processed during CronJob with fullAuth and MasterCard + DELAY
		$this->reAuthorize($this->_MasterCardCustomerId, 'mc', '4444', 100.00, 100.00, false, true, 24);
		#Test Reauthorize processed during CronJob with fullAuth and Visa + Error date + Auth Later than Error
		$this->reAuthorize($this->_VisaCustomerId, 'visa', '1111', 100.00, 100.00, false, true, 24, true, true);
		#Test Reauthorize processed during CronJob with fullAuth and MasterCard + Error date + Auth Later than Error
		$this->reAuthorize($this->_MasterCardCustomerId, 'mc', '4444', 100.00, 100.00, false, true, 24, true, true);
		#Test Reauthorize processed during CronJob with fullAuth and Visa + Error date + Auth Earlier than Error
		$this->reAuthorize($this->_VisaCustomerId, 'visa', '1111', 100.00, 100.00, false, true, 24, true, false);
		#Test Reauthorize processed during CronJob with fullAuth and MasterCard + Error date + Auth Earlier than Error
		$this->reAuthorize($this->_MasterCardCustomerId, 'mc', '4444', 100.00, 100.00, false, true, 24, true, false);
	}

	public function reAuthorize($customerId, $type, $card_number, $total, $authTotal, $fullAmount = false, $voidTransaction = false, $delay = 0, $error = false, $auth_record_later = false) {
		$ordersCollection = Order::Collection();
		#Create Temporary order
		$order = Order::create(array('_id' => new MongoId()));
		$order->order_id = strtoupper(substr((string)$order->_id, 0, 8) . substr((string)$order->_id, 13, 4));
		$cybersource = new CyberSource(Processor::config('test'));
		$profile = $cybersource->profile($customerId);
		#Create Transaction initial Transaction in CyberSource
		$authorizeObject = Processor::authorize('test', $authTotal, $profile, array('orderID' => $order->order_id));
		$this->assertTrue($authorizeObject->success());		
		#Temporary User Creation
		$user = User::create(array('_id' => new MongoId()));
		$user->save($this->_UserInfos);
		$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
		if($error) {
			$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - 30, date("Y")));
			if($auth_record_later) { 
				$auth_records[0]['date_saved'] = new MongoDate(mktime(date("H"), date("i"), date("s"), date("m"),  date("d") - ($this->_ReAuthLimitDate + 1), date("Y")));
			} else {
				$auth_records[0]['date_saved'] = new MongoDate(mktime(date("H"), date("i"), date("s"), date("m"),  date("d") - ($this->_ReAuthLimitDate + 3), date("Y")));
			}
			$order->auth_records = $auth_records;
			$order->error_date = new MongoDate(mktime(date("H"), date("i"), date("s"), date("m"), date("d") - ($this->_ReAuthLimitDate + 2) , date("Y")));
		} else {
			$order->date_created = new MongoDate(mktime(0, 0, 0, date("m"), date("d") - $this->_ReAuthLimitDate, date("Y")));
		}
		$order->save(array(
				'total' => $total,
				'card_type' => $type,
				'card_number' => $card_number,
				'authKey' => $authorizeObject->key,
				'auth' => $authorizeObject->export(),
				'processor' => $authorizeObject->adapter,
				'authTotal' => $authTotal,
				'cyberSourceProfileId' => $customerId,
				'user_id' => (string) $user->_id,
				'billing' => $this->_billingAddress,
				'test' => true
		));
		If(!$fullAmount && $voidTransaction) {
			#Running Li3 command Void
			$VoidTransaction = new VoidTransaction();
			$VoidTransaction->unitTest = true;
			$VoidTransaction->run();
			#Get Order Modified
			$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
			if(isset($order_test['void_records'])) {
				$void_records = $order_test['void_records'];
				$void_records[0]['date_saved'] = new MongoDate(mktime(date("H") - $delay, date("i"), date("s"), date("m"), date("d"), date("Y")));
				$update = $ordersCollection->update(
					array('_id' => $order->_id),
					array('$set' => array('void_records' => $void_records)), array( 'upsert' => true)
				);
			}
		}
		#Running Li3 command Reauthorize
		$orders = $ordersCollection->find(array(
			'test' => true,
			'cancel' => array('$ne' => true)
		));
		$ReAuthorize = new ReAuthorize();
		$ReAuthorize->unitTest = true;
		$ReAuthorize->fullAmount = $fullAmount;
		$ReAuthorize->orders = $orders;
		$ReAuthorize->run();
		#Get Order Modified
		$order_test = $ordersCollection->findOne(array("_id" => $order->_id));
		#Testing Modifications
		if($error) {
			if($auth_record_later) {
				$this->assertTrue($order_test['authKey'] != $order->authKey);
				$this->assertTrue($order_test['authTotal'] == $total);
				$this->assertTrue(!empty($order_test['auth_records'][1]));
			} else {
				$this->assertTrue(empty($order_test['auth_records'][1]));
			}
		} else { 
			if(!$fullAmount && ($total != $authTotal) && $voidTransaction) {
				$this->assertTrue($order_test['authKey'] == $order->authKey);
				$this->assertTrue($order_test['authTotal'] != $total);
				$this->assertTrue(empty($order_test['auth_records']));
			} else if(!$fullAmount && $total == $authTotal && $voidTransaction && empty($delay)) {
				$this->assertTrue($order_test['authKey'] != $order->authKey);
				$this->assertTrue($order_test['authTotal'] == $total);
				$this->assertTrue(empty($order_test['auth_records']));
			} else if (!$fullAmount && $total == $authTotal && $voidTransaction && $delay) {
				$this->assertTrue($order_test['authKey'] != $order->authKey);
				$this->assertTrue($order_test['authTotal'] == $total);
				$this->assertTrue(!empty($order_test['auth_records']));
			} else if (!$fullAmount && $total == $authTotal && !$voidTransaction) {
				$this->assertTrue($order_test['authKey'] == $order->authKey);
				$this->assertTrue($order_test['authTotal'] != $total);
				$this->assertTrue(empty($order_test['auth_records']));
			} else if($fullAmount) {
				$this->assertTrue($order_test['authKey'] != $order->authKey);
				$this->assertTrue($order_test['authTotal'] == $total);
				$this->assertTrue(!empty($order_test['auth_records']));
			}
		}
		#Delete Temporary Documents
		User::remove(array("_id" => $user->_id));
		Order::remove(array("_id" => $order->_id));
	}
}