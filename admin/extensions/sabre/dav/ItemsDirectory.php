<?php

namespace admin\extensions\sabre\dav;

use admin\models\Event;
use admin\models\Item;
use admin\extensions\sabre\dav\ItemDirectory;
use Sabre_DAV_Exception_FileNotFound;

class ItemsDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function __construct(array $config = array()) {
		parent::__construct($config + array('value' => Item::meta('source')));
	}

	public function __toString() {
		return '_'. $this->_config['value'];
	}

	public function getChild($name) {
		return new ItemDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$url = $this->getParent()->getValue();
		$id = Event::first(array('conditions' => compact('url')))->_id;
		$items = Item::all(array(
			'conditions' => array(
				'event' => (string) $id
			)
		));
		$children = array();
		foreach ($items as $item) {
			$children[] = new ItemDirectory(array('value' => $item->url, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$url = $this->getParent()->getValue();
		$id = Event::first(array('conditions' => compact('url')))->_id;

		return (boolean) Item::first(array(
			'conditions' => array(
				'url' => $name,
				'event' => $id
			)
		));
	}
}

?>