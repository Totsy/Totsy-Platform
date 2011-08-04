<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\ItemFile;
use admin\models\File;

class ItemDirectory extends \admin\extensions\sabre\dav\Directory {

	public function getChild($name) {
		// return new ItemFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		/*
		$data = ::find('all', array(
			'conditions' => array(
				'url' => $this->getValue()
			)
		));

		$children = array();
		foreach ($data as $item) {
			$children[] = new ItemDirectory(array('value' => $item->url, 'parent' => $this));
		}
		return $children;
		*/
	}

	public function childExists($name) {
		return false;
	}
}

?>