<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\EventFile;
use admin\models\File;
use admin\models\Event;
use Sabre_DAV_Exception_FileNotFound;

class EventDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

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

		$children = array();
		foreach ($item->images as $name => $id) {
			$children[] = new EventFile(array('value' => $name, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		$item = $this->_item();

		return isset($item->images[$name]);
	}

	public function createFile($name, $data = null) {
		$file = File::write($data, compact('name'));
		$name = pathinfo($name, PATHINFO_FILENAME);
		$item = $this->_item();

		$item->images = array(
			$name => $file->_id
		) + $item->images->data();

		return (boolean) $item->save();
	}

	protected function _item() {
		return Event::first(array(
			'conditions' => array('url' => $this->getValue())
		));
	}
}

?>