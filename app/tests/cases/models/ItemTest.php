<?php

namespace app\tests\cases\models;

use app\models\User;
use app\models\Item;
use li3_fixtures\test\Fixture;
use lithium\storage\Session;
use app\tests\mocks\storage\session\adapter\MemoryMock;
use lithium\data\collection\DocumentArray;

class ItemTest extends \lithium\test\Unit {
	public $user;
	protected $_delete = array();

    public function setUp() {
		$fixture = Fixture::load('Item');
		$next = $fixture->first();
		do {
			Item::remove(array('_id' => $next['_id'] ));
			$item = Item::create();
			$item->save($next);
		} while ($next = $fixture->next());

		$data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'email' => uniqid('george') . '@example.com',
			'total_credit' => 30
		);
		$this->user = User::create();
		$this->user->save($data, array('validate' => false));
		$this->_delete[] = $this->user;

		Session::config(array(
			'default' => array('adapter' => new MemoryMock())
		));
	}

	public function tearDown() {
		$fixture = Fixture::load('Item');
		$item = $fixture->first();
		do {
			Item::remove( array( '_id' => $item['_id'] ) );
		} while ($item = $fixture->next());

		foreach ($this->_delete as $document) {
			$document->delete();
		}
	}

	public function testSizes() {
		$item = Item::find('first', array('conditions' => array('_id' => '10007')));
		$this->assertTrue(!empty($item));

		$result = Item::sizes($item);
		$expected = array(4, 5, 6, 7);
		$this->assertEqual($expected, $result);
	}
}

?>
