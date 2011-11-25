<?php

namespace app\tests\cases\models;

use app\models\Base;
use MongoDate;

class BaseTest extends \lithium\test\Unit {
	public function testDates() {
		$result = Base::dates('now');
		$this->assertTrue($result instanceof MongoDate);
	}

	public function testGenerateToken() {
		$token1 = Base::generateToken();
		$token2 = Base::generateToken();
		$this->assertTrue(is_string($token1), $token1);
		$this->assertEqual(10, strlen($token1));
		$this->assertNotEqual($token2, $token1);
	}

	public function testRandomString() {
		$random1 = Base::randomString(8);
		$random2 = Base::randomString(8);
		$this->assertTrue(is_string($random1), $random1);
		$this->assertEqual(8, strlen($random1));
		$this->assertNotEqual($random2, $random1);
	}

	public function testRandomStringNoRepeatingCharacters() {
		$random = Base::randomString(4, 'AB');
		$this->assertTrue(in_array($random, array('ABAB', 'BABA')), $random);
	}
}

?>