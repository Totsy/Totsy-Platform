<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\YearDirectory;

class ModelDirectory extends \admin\extensions\sabre\dav\Directory {

	public function __toString() {
		$model = $this->_config['value'];
		return $model::meta('source');
	}

	public function getChild($name) {
		return new YearDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach ($this->_range() as $year) {
			$children[] = new YearDirectory(array('value' => $year, 'parent' => $this));
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