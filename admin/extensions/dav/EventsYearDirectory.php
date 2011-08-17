<?php

namespace admin\extensions\dav;

use admin\extensions\dav\EventsMonthDirectory;

class EventsYearDirectory extends \admin\extensions\dav\GenericDirectory {

	public function getChild($name) {
		return new EventsMonthDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach ($this->_range() as $month) {
			$children[] = new EventsMonthDirectory(array('value' => $month, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		return in_array($name, $this->_range());
	}

	protected function _range() {
		return range(1, 12);
	}
}

?>