<?php

namespace admin\extensions\dav;

use admin\extensions\dav\EventDirectory;
use admin\models\Event;
use MongoDate;
use DateTime;
use DateInterval;

class EventsMonthDirectory extends \admin\extensions\dav\GenericDirectory {

	public function getChild($name) {
		return new EventDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$items = Event::all(array(
			'conditions' => $this->_conditions()
		));

		$children = array();
		foreach ($items as $item) {
			$children[] = new EventDirectory(array('value' => $item->url, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		return (boolean) Event::first(array(
			'conditions' => array('url' => $name)
		));
	}

	protected function _conditions() {
		$month = (string) $this;
		$year  = (string) $this->getParent();

		$current = DateTime::createFromFormat('Y-m-d', "{$year}-{$month}-01");

		$start = $current->getTimestamp();
		$end = $current->add(new DateInterval('P1M'))->getTimestamp();

		return array(
			'created_date' => array(
				'$gte' => new MongoDate($start),
				'$lt' => new MongoDate($end)
			)
		);
	}
}

?>