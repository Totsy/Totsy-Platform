<?php

namespace admin\extensions\sabre\dav;

use admin\models\ItemImage;
use admin\models\Item;

class ItemFile extends \admin\extensions\sabre\dav\GenericFile {

	public function put($data) {
		$position = $this->getParent()->getValue();
		$item = $this->_item();

		$file = ItemImage::resizeAndSave($position, $data);

		if (($value = $this->getParent()->getValue()) == 'alternate') {
			$images = $item->alternate_images->data();

			if (!in_array($file->_id, $images)) {
				$images[] = $file->_id;
			}
			$item->alternate_images = $images;
		} else {
			$item->{"{$value}_image"} = $file->_id;
		}
		return (boolean) $item->save();
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		$item = $this->_item();

		if (($value = $this->getParent()->getValue()) == 'alternate') {
			$images = $item->alternate_images->data();

			$key = array_search((string) $file->_id, $images);
			unset($images[$key]);

			$item->alternate_images = $images;
		} else {
			$item->{"{$value}_image"} = null;
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