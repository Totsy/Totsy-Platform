<?php

namespace admin\extensions\dav;

use admin\models\ItemImage;
use admin\models\Item;

class ItemFile extends \admin\extensions\dav\GenericFile {

	public function put($data) {
		$position = $this->getParent()->getValue();
		$item = $this->_item();

		$file = ItemImage::resizeAndSave($position, $data);

		$item->attachImage($position, $file->_id);
		return $item->save(null, Item::imagesWhitelist());
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		$position = $this->getParent()->getValue();
		$item = $this->_item();

		$item->detachImage($position, $file->_id);
		return $item->save(null, Item::imagesWhitelist());
	}

	protected function _item() {
		return Item::first(array(
			'conditions' => array(
				'url' => $this->getParent()->getParent()->getValue()
			)
		));
	}

	protected function _file() {
		return ItemImage::first(array('conditions' => array('_id' => $this->getValue())));
	}
}

?>