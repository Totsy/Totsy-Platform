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

	protected function assertLoaded($fixtures = null) {
		$failed = $fixtures ? array_intersect($this->failed_fixtures, (array) $fixtures) : $this->failed_fixtures;
		$users = $this->users;
		$errors = function ($short) use ($users) { return $short . " (" . join(", ", array_keys($users[$short]->errors())) . ")"; };
		$this->assertTrue(empty($failed), "Some fixtures failed to load: " . join(", ", array_map($errors, $failed)));
	}

	protected function user($short) {
		$_id = $this->users[$short] ? $this->users[$short]->_id : null;
		return $_id ? User::find($this->users[$short]->_id) : null;
	}

	protected function fakeSession($short) {
		$this->sessionSave = Session::read('userLogin');
		Session::write('userLogin', array('_id' => $this->users[$short]->_id));
	}

	protected function unfakeSession() {
		Session::write('userLogin', $this->sessionSave);
	}

	public function testLookup() {
		$users = array('user1', 'user2');
		$this->assertLoaded($users);
		$this->assertNull(User::lookup('invalid'));
		foreach ($users as $short) {
			$user = $this->users[$short];
			foreach (array('email', '_id') as $attr) {
				$result = User::lookup($user->$attr);
				$this->assertFalse($result === null, "Lookup failed for {$short} - {$attr}");
				if ($result) {
					$this->assertEqual($user->_id, $result->_id);
				}
			}
		}
	}

	public function testLog() {
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