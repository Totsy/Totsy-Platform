<?php

namespace admin\extensions\sabre\dav;

use admin\models\File;
use admin\models\Event;

class EventFile extends \admin\extensions\sabre\dav\GenericFile {

	public function put($data) {
		$file = File::write($data);
		$item = $this->_item();

		$item->images = array(
			$this->getParent()->getValue() . '_image' => $file->_id
		) + $item->images->data();

		return $item->save();
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		$item = $this->_item();

		$images = $item->images->data();
		unset($images[$this->getParent()->getValue() . '_image']);
		$item->images = $images;

		return (boolean) $item->save();
	}

	protected function _item() {
		return Event::first(array(
			'conditions' => array(
				'url' => $this->getParent()->getParent()->getValue()
			)
		));
	}

	protected function _file() {
		return File::first(array('conditions' => array('_id' => $this->getValue())));
	}
}

?>