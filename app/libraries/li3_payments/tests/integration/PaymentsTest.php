<?php

namespace li3_payments\tests\integration;

use li3_payments\extensions\Payments;

class PaymentsTest extends \lithium\test\Unit {

	public function skip() {
		$this->skipIf(!Payments::config('test'), 'No "test" payment configuration defined.');
	}

	protected $_classes = array(
		'view' => 'lithium\template\View',
		'service' => 'lithium\net\http\Service',
		'response' => 'lithium\net\http\Response',
		'payments' => 'li3_payments\extensions\Payments'
	);

	public function testProfileCrudAndPaymentOperations() {
		$address = Payments::create('test', 'address', array(
			'firstName' => 'John',
			'lastName'  => 'Smith',
			'address'   => '1001 6th Ave.',
			'city'      => 'New York',
			'state'     => 'NY',
			'zip'       => '10001'
		));
		$customer = Payments::create('test', 'customer', array(
			'firstName' => 'John',
			'lastName' => 'Smith',
			'email' => 'john.smith@gmail.com',
			'address' => $address,
			'shipping' => $address,
			'billing' => $address,
			'payment' => Payments::create('test', 'creditCard', array(
				'number' => '4111111111111111',
				'month' => 11,
				'year' => 2023
			))
		));
		$this->assertTrue($customer->save());

		$address = Payments::create('test', 'address', array(
			'firstName' => 'Bob',
			'lastName'  => 'Jones',
			'address'   => '1002 6th Ave.',
			'city'      => 'New York',
			'state'     => 'NY',
			'zip'       => '10001'
		));
		$customer = Payments::create('test', 'customer', array(
			'firstName' => 'Bob',
			'lastName' => 'Jones',
			'email' => 'bob.jones@gmail.com',
			'address' => $address,
			'shipping' => $address,
			'billing' => $address,
			'payment' => Payments::create('test', 'creditCard', array(
				'number' => '4111111111111111',
				'month' => 11,
				'year' => 2021
			))
		));
		$this->assertTrue($customer->save());

		$ids = Payments::profiles('test');
		$this->assertTrue(is_array($ids));
		$this->assertEqual(2, count($ids));

		$this->assertTrue(is_numeric($ids[0]));
		$this->assertTrue(is_numeric($ids[1]));

		$customer = Payments::profiles('test', $ids[0]);

		$this->assertTrue(is_numeric($customer->key));
		$this->assertEqual('individual', $customer->type);
		$this->assertEqual('john.smith@gmail.com', $customer->email);
		$this->assertTrue(is_object($customer->payment));
		$this->assertTrue($customer->payment->number);
		$this->assertTrue($customer->payment->year);

		$this->assertEqual('John', $customer->billing->firstName);
		$this->assertEqual('Smith', $customer->billing->lastName);
		$this->assertEqual('1001 6th Ave.', $customer->billing->address);

		$transactionId = Payments::process('test', intval(rand(5, 10)), $customer);
		$this->assertTrue(is_numeric($transactionId));
		$this->assertTrue(is_object($customer));

		if (is_object($customer)) {
			$this->assertTrue($customer->delete());
		}

		$customer2 = Payments::profiles('test', $ids[1]);

		$this->assertTrue(is_numeric($customer2->key));
		$this->assertEqual('individual', $customer2->type);
		$this->assertEqual('bob.jones@gmail.com', $customer2->email);
		$this->assertTrue(is_object($customer2->payment));
		$this->assertTrue($customer2->payment->number);
		$this->assertTrue($customer2->payment->year);

		$this->assertEqual('Bob', $customer2->billing->firstName);
		$this->assertEqual('Jones', $customer2->billing->lastName);
		$this->assertEqual('1002 6th Ave.', $customer2->billing->address);

		$transactionId = Payments::process('test', intval(rand(5, 10)), $customer2);
		$this->assertTrue(is_numeric($transactionId));
		$this->assertTrue(is_object($customer2));

		if (is_object($customer2)) {
			$this->assertTrue($customer2->delete());
		}
	}

	public function testPreAuthCaptureTransaction() {
		$amt = round((rand() % 2000) / 100, 2);

		$transId = Payments::authorize('test', $amt, Payments::create('test', 'creditCard', array(
			'number' => '4111111111111111',
			'month' => 11,
			'year' => 2023
		)));
		$this->assertTrue(is_numeric($transId));

		$captured = Payments::capture('test', $transId, $amt);
		$this->assertTrue(is_numeric($transId));
	}
}

?>