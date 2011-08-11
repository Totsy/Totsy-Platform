<?php

namespace admin\tests\cases\models;

use admin\models\Event;
use li3_fixtures\test\Fixture;

class EventTest extends \lithium\test\Unit {

	public function testUpdateKeepsModifications() {
		$fixtures = Fixture::load('Event');

		$event = Event::create();
		$event->save($fixtures->first());

		$id = $event->_id;

		$event->images = array('foo' => 'bar');
		$event->save();

		$result = Event::first(array('conditions' => array('_id' => $id)))->data();
		$this->assertTrue(isset($result['modifications']));
		$this->assertFalse(isset($result['']['modifications']));

		$event->delete();
	}
}

?>