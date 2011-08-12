<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\EventFile;
use admin\models\Event;
use admin\models\EventImage;
use Sabre_DAV_Exception_FileNotFound;

class EventImageDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function getChild($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		return new EventFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$type = EventImage::$type[$this->getValue()];
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
		$file = EventImage::resizeAndSave($this->getValue(), $data, compact('name'));
		$type = EventImage::$type[$this->getValue()];
		$item = $this->_item();

		$item->images = array(
			$type['field'] => $file->_id
		) + $item->images->data();

		return (boolean) $item->save();
	}

	protected function _item() {
		return Event::first(array(
			'conditions' => array('url' => $this->getParent()->getValue())
		));
	}
}

?>