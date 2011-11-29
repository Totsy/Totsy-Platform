<?php

namespace app\tests\cases\models;

use app\models\User;
use lithium\storage\Session;
use MongoId;
use li3_fixtures\test\Fixture;

class UserTest extends \lithium\test\Unit {
	public function setUp() {
		$this->users = Fixture::load('User')->map(function ($fixture) {
			return User::create($fixture);
		});
		$this->failed_fixtures = array();
		foreach ($this->users as $short => $user) {
			if (!$user->save()) {
				$this->failed_fixtures[] = $short;
			}
		}
	}

	public function tearDown() {
		foreach ($this->users as $short => $user) {
			$user->delete();
		}
	}

	protected function _assertLoaded($fixtures = null) {
		$failed = $fixtures ? array_intersect($this->failed_fixtures, (array) $fixtures) : $this->failed_fixtures;
		$users = $this->users;
		$errors = function ($short) use ($users) { return $short . " (" . join(", ", array_keys($users[$short]->errors())) . ")"; };
		$this->assertTrue(empty($failed), "Some fixtures failed to load: " . join(", ", array_map($errors, $failed)));
	}

	protected function _user($short) {
		$_id = $this->users[$short] ? $this->users[$short]->_id : null;
		return $_id ? User::find($this->users[$short]->_id) : null;
	}

	protected function _fakeSession($short) {
		$this->sessionSave = Session::read('userLogin');
		Session::write('userLogin', array('_id' => $this->users[$short]->_id));
	}

	protected function _unfakeSession() {
		Session::write('userLogin', $this->sessionSave);
	}

	public function testLookup() {
		$users = array('user1', 'user2');
		$this->_assertLoaded($users);

		$result = User::lookup('invalid');
		$this->assertNull($result);

		foreach ($users as $short) {
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
		$this->_assertLoaded('user1');
		$old = $this->_user('user1');
		$this->_fakeSession('user1');

		$result = User::log('1.2.3.4');
		$this->assertTrue($result);

		$this->_unfakeSession();
		$new = $this->_user('user1');

		$expected = $old->logincounter + 1;
		$result = $new->logincounter;
		$this->assertEqual($expected, $result);

		$expected = '1.2.3.4';
		$result = $new->lastip;
		$this->assertEqual($expected, $result);

		$expected = $old->lastlogin;
		$result = $new->lastlogin;
		$this->assertNotEqual($expected, $result);
	}

	public function testApplyCredit() {
		$this->_assertLoaded('user1');
		$old = $this->_user('user1');

		$result = User::applyCredit($old->_id, 100);
		$this->assertTrue($result);

		$new = $this->_user('user1');

		$expected = $old->total_credit + 100;
		$result = $new->total_credit;
		$this->assertEqual($expected, $result);
	}
}

?>