<?php

namespace admin\extensions\dav;

use admin\models\ItemImage;
use admin\models\Item;
use admin\models\Event;

class ItemFile extends \admin\extensions\dav\GenericFile {

	public function put($data) {
		$position = $this->getParent()->getValue();
		$item = $this->_item();

		$file = ItemImage::resizeAndSave($position, $data);
		$item->attachImage($position, $file->_id);

		return true;
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
		/* Gets value from EventDirectory. */
		$url = $this->getParent()->getParent()->getParent()->getParent()->getValue();
		$id = Event::first(array('conditions' => compact('url')))->_id;
		return Item::first(array(
			'conditions' => array(
				'vendor_style' => $this->getParent()->getParent()->getValue(),
				'event' => (string) $id
			)
		));
	}

	protected function _file() {
		return ItemImage::first(array('conditions' => array('_id' => $this->getValue())));
	}
}

?>