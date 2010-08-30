<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of Rad, Inc. (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_payments\tests\cases\extensions\adapter\payments;

use li3_payments\extensions\Payments;
use li3_payments\extensions\payments\Customer;
use li3_payments\extensions\payments\Transaction;
use li3_payments\extensions\adapter\payments\AuthorizeNet;

class AuthorizeNetTest extends \lithium\test\Unit {

	public function setUp() {
		$this->subject = new AuthorizeNet(array('login' => 'foo', 'key' => 'bar'));
	}

	public function tearDown() {
	}

	public function testCreateCustomer() {
		$customer = new Customer();
	}

	public function testAuthorizeTransaction() {
		
	}
}

?>