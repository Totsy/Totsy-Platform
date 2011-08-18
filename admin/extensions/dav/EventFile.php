<?php

namespace admin\extensions\dav;

use admin\models\EventImage;
use admin\models\Event;

class EventFile extends \admin\extensions\dav\GenericFile {

	public function put($data) {
		$position = $this->getParent()->getValue();
		$item = $this->_item();

		$file = EventImage::resizeAndSave($position, $data);
		$item->attachImage($position, $file->_id);

		return $item->save();
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		$position = $this->getParent()->getValue();
		$item = $this->_item();

		$item->detachImage($position, $file->_id);

		return $item->save();
	}

	protected function _item() {
		return Event::first(array(
			'conditions' => array(
				'url' => $this->getParent()->getParent()->getValue()
			)
		));
	}

	protected function _file() {
		return EventImage::first(array('conditions' => array('_id' => $this->getValue())));
	}
}

?>