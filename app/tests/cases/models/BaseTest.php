<?php

namespace app\tests\cases\models;

use app\models\Base;
use MongoDate;

class BaseTest extends \lithium\test\Unit {
	public function testDates() {
		$date = Base::dates('now');
		$result = $date instanceof MongoDate;
		$this->assertTrue($result);
	}

	public function testGenerateToken() {
		$token1 = Base::generateToken();
		$token2 = Base::generateToken();

		$result = is_string($token1);
		$message = $token1;
		$this->assertTrue($result, $message);

		$expected = 10;
		$result = strlen($token1);
		$this->assertEqual($expected, $result);

		$expected = $token2;
		$result = $token1;
		$this->assertNotEqual($expected, $result);
	}

	public function testRandomString() {
		$random1 = Base::randomString(8);
		$random2 = Base::randomString(8);

		$result = is_string($random1);
		$message = $random1;
		$this->assertTrue($result, $message);

		$expected = 8;
		$result = strlen($random1);
		$this->assertEqual($expected, $result);

		$expected = $random2;
		$result = $random1;
		$this->assertNotEqual($expected, $result);
	}

	public function testRandomStringNoRepeatingCharacters() {
		$random = Base::randomString(4, 'AB');
		$result = in_array($random, array('ABAB', 'BABA'));
		$message = $random;
		$this->assertTrue($result, $message);
	}
}

?>