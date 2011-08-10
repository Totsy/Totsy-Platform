<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\EventFile;
use admin\models\File;
use admin\models\Event;
use admin\models\EventImage;
use Sabre_DAV_Exception_FileNotFound;

class EventImageDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function getChild($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		$item = $this->_item();

		if (!isset($item->images[$name])) {
			throw new Sabre_DAV_Exception_FileNotFound("File `{$name}` not found,");
		}
		return new EventFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$item = $this->_item();

		if (!isset($item->images[$this->getValue() . '_image'])) {
			return array();
		}
		$file = File::first(array(
			'conditions' => array('_id' => $item->images[$this->getValue() . '_image'])
		));

		return array(
			new EventFile(array('value' => $file->basename(), 'parent' => $this))
		);
	}

	public function childExists($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);

		return (boolean) File::first($name);
	}

	public function createFile($name, $data = null) {
		$file = File::write($data, compact('name'));
		$item = $this->_item();

		$item->images = array(
			$this->getValue() . '_image' => $file->_id
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