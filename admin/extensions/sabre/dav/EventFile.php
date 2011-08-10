<?php

namespace admin\extensions\sabre\dav;

use admin\models\File;
use admin\models\Event;

class EventFile extends \admin\extensions\sabre\dav\GenericFile {

	public function put($data) {
		if ($file = $this->_file()) { /* This object represent an existing file. */
			$count = File::used($file->_id);

			/* This object is included in the count. */
			if ($count === 1) {
				$file->delete();
			}
		}
		$file = File::write($data);
		$item = $this->_item();

		$item->images = array(
			$this->getValue() => $file->_id
		) + $item->images->data();

		return $item->save();
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		$item = $this->_item();

		$images = $item->images->data();
		unset($images[$this->getValue()]);
		$item->images = $images;

		return (boolean) $item->save();
	}

	protected function _item() {
		return Event::first(array(
			'conditions' => array(
				'url' => $this->getParent()->getValue()
			)
		));
	}

	protected function _file() {
		$item = $this->_item();

		return File::first(array(
			'conditions' => array(
				'_id' => $item->images[$this->getValue()]
			)
		));
	}
}

?>