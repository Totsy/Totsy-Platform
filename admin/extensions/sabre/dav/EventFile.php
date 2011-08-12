<?php

namespace admin\extensions\sabre\dav;

use admin\models\EventImage;
use admin\models\Event;

class EventFile extends \admin\extensions\sabre\dav\GenericFile {

	public function put($data) {
		$position = $this->getParent()->getValue();
		$type = EventImage::$type[$this->getParent()->getValue()];
		$item = $this->_item();

		$file = EventImage::resizeAndSave($position, $data);

		$item->images = array(
			$type['field'] => $file->_id
		) + $item->images->data();

		return $item->save();
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		$type = EventImage::$type[$this->getParent()->getValue()];
		$item = $this->_item();

		$images = $item->images->data();
		unset($images[$type['field']]);
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
		return EventImage::first(array('conditions' => array('_id' => $this->getValue())));
	}
}

?>