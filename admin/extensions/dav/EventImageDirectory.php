<?php

namespace admin\extensions\dav;

use admin\extensions\dav\EventFile;
use admin\models\Event;
use admin\models\EventImage;
use Sabre_DAV_Exception_FileNotFound;

class EventImageDirectory extends \admin\extensions\dav\GenericDirectory {

	public function getChild($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		return new EventFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$type = EventImage::$types[$this->getValue()];
		$item = $this->_item();

		$children = array();

		if (!isset($item->images[$type['field']])) {
			return array();
		}
		$file = EventImage::first(array(
			'conditions' => array('_id' => $item->images[$type['field']])
		));

		if ($file) {
			$children[] = new EventFile(array('value' => $file->_id, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);

		return (boolean) EventImage::first($name);
	}

	public function createFile($name, $data = null) {
		$position = $this->getValue();
		$file = EventImage::resizeAndSave($position, $data, compact('name'));
		$item = $this->_item();

		$item->attachImage($position, $file->_id);

		return $item->save();
	}

	protected function _item() {
		return Event::first(array(
			'conditions' => array('url' => $this->getParent()->getValue())
		));
	}
}

?>