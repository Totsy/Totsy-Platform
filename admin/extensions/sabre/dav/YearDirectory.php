<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\MonthDirectory;

class YearDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function getChild($name) {
		return new MonthDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach ($this->_range() as $month) {
			$children[] = new MonthDirectory(array('value' => $month, 'parent' => $this));
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