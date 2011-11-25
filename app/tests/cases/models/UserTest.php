<?php

namespace app\tests\cases\models;

use app\models\User;
use lithium\storage\Session;
use MongoId;
use li3_fixtures\test\Fixture;

class UserTest extends \lithium\test\Unit {
	public function setUp() {
		$this->sessionConfig = Session::Config();
		Session::config(array(
			'default' => array('adapter' => 'app\tests\mocks\storage\session\adapter\MemoryMock'),
			'cookie' => array('adapter' => 'app\tests\mocks\storage\session\adapter\MemoryMock')
		));
		$this->users = Fixture::load('User')->map(function ($fixture) {
			/* Ensure we're always able to insert this record. */
			$fixture['confirmemail'] = $fixture['email'] = uniqid('user') . '@example.com';
			$user = User::create($fixture);
			$user->save(null, array('validate' => false));
			return $user;
		});
	}

	public function tearDown() {
		foreach ($this->users as $user) {
			$user->delete();
		}
		Session::Config($this->sessionConfig);
	}

	protected function _user($short) {
		$_id = $this->users[$short] ? $this->users[$short]->_id : null;
		return $_id ? User::find($this->users[$short]->_id->{'$id'}) : null;
	}

	public function testLookup() {
		$result = User::lookup('invalid');
		$this->assertNull($result);

		foreach (array('user1', 'user2') as $short) {
			$user = $this->users[$short];
			foreach (array('email', '_id') as $attr) {
				$lookup = User::lookup($user->$attr);

				$result = is_null($lookup);
				$message = "Lookup failed for {$short} - {$attr}";
				$this->assertFalse($result, $message);

				if ($lookup) {
					$expected = $user->_id;
					$result = $lookup->_id;
					$this->assertEqual($expected, $result);
				}
			}
		}
	}

	public function testLog() {
		$old = $this->users['user1'];
		Session::write('userLogin', $old->data());

		$result = User::log('1.2.3.4');
		$this->assertTrue($result);

		$updated = $this->_user('user1');

		$expected = $old->logincounter + 1;
		$result = $updated->logincounter;
		$this->assertEqual($expected, $result);

		$expected = '1.2.3.4';
		$result = $updated->lastip;
		$this->assertEqual($expected, $result);

		$expected = $old->lastlogin;
		$result = $updated->lastlogin;
		$this->assertNotEqual($expected, $result);
	}

	public function testApplyCredit() {
		$old = $this->users['user1'];
		$result = User::applyCredit($old->_id, 100);
		$this->assertTrue($result);

		$updated = $this->_user('user1');
		$expected = $old->total_credit + 100;
		$result = $updated->total_credit;
		$this->assertEqual($expected, $result);

		$this->assertLoaded('user1');
		$old = $this->user('user1');
		$this->fakeSession('user1');
		$this->assertTrue(User::log('1.2.3.4'));
		$this->unfakeSession();
		$new = $this->user('user1');
		$this->assertEqual($old->logincounter + 1, $new->logincounter);
		$this->assertEqual('1.2.3.4', $new->lastip);
		$this->assertNotEqual($old->lastlogin, $new->lastlogin);
	}

	public function testApplyCredit() {
		$this->assertLoaded('user1');
		$old = $this->user('user1');
		$this->assertTrue(User::applyCredit($old->_id, 100));
		$new = $this->user('user1');
		$this->assertEqual($old->total_credit + 100, $new->total_credit);
	}
}

?>