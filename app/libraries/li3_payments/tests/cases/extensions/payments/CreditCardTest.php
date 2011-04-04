<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of Rad, Inc. (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_payments\tests\cases\extensions\payments;

use li3_payments\extensions\payments\CreditCard;

class CreditCardTest extends \lithium\test\Unit {

	public function setUp() {
	}

	public function tearDown() {
	}

	/**
	 * Tests that a `CreditCard` object can be constructed with a set of paramaeters and validated.
	 *
	 * @return void
	 */
	public function testCreateAndValidate() {
		$card = new CreditCard(array(
			'number' => '5449178787344094',
			'type' => 'mc',
			'name' => 'Bob',
			'code' => '737'
		));
		$this->assertTrue($card->validates());
		$this->assertFalse($card->errors());

		$card->number = '4111111111111111';
		$this->assertFalse($card->validates());
		$this->assertEqual(array('number'), array_keys($card->errors()));
	}
}

?>