<?php

namespace admin\extensions\dav;

use admin\models\ItemImage;
use admin\models\Item;

class ItemFile extends \admin\extensions\dav\GenericFile {

	public function put($data) {
		$position = $this->getParent()->getValue();
		$type = ItemImage::$types[$this->getParent()->getValue()];
		$item = $this->_item();

		$file = ItemImage::resizeAndSave($position, $data);

		if ($type['multiple']) {
			$images = $item->{$type['field']}->data();

			if (!in_array($file->_id, $images)) {
				$images[] = $file->_id;
			}
			$item->{$type['field']} = $images;
		} else {
			$item->{$type['field']} = $file->_id;
		}
		return (boolean) $item->save();
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		$type = ItemImage::$types[$this->getParent()->getValue()];
		$item = $this->_item();

		if ($type['multiple']) {
			$images = $item->{$type['field']}->data();

			$key = array_search((string) $file->_id, $images);
			unset($images[$key]);

			$item->{$type['field']} = $images;
		} else {
			$item->{$type['field']} = null;
		}
		return (boolean) $item->save();
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