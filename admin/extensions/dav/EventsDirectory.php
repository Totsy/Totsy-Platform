<?php

namespace admin\extensions\dav;

use admin\models\Event;
use admin\extensions\dav\EventsYearDirectory;

class EventsDirectory extends \admin\extensions\dav\GenericDirectory {

	public function __construct(array $config = array()) {
		parent::__construct($config + array('value' => Event::meta('source')));
	}

	public function __toString() {
		return $this->_config['value'];
	}

	public function getChild($name) {
		return new EventsYearDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach ($this->_range() as $year) {
			$children[] = new EventsYearDirectory(array('value' => $year, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		return in_array($name, $this->_range());
	}

	protected function _range() {
		$min = date('Y') - 10;
		$max = date('Y');

		return range($min, $max);
	}
}

?>