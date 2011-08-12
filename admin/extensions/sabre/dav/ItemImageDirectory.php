<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\ItemFile;
use admin\models\Item;
use admin\models\ItemImage;
use Sabre_DAV_Exception_FileNotFound;

class ItemImageDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function getChild($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		return new ItemFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$item = $this->_item();
		$children = array();

		if (ItemImage::$types[$value = $this->getValue()]['multiple']) {
			if (!$item->{"{$value}_images"}) {
				return $children;
			}
			foreach ($item->{"{$value}_images"} as $id) {
				$children[] = new ItemFile(array('value' => $id, 'parent' => $this));
			}
			return $children;
		}

		if ($id = $item->{"{$value}_image"}) {
			$children[] = new EventFile(array('value' => $id, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		$item = $this->_item();

		if (ItemImage::$types[$value = $this->getValue()]['multiple']) {
			if (!$item->{"{$value}_images"}) {
				return false;
			}
			return in_array($name, $item->{"{$value}_images"}->data());
		}
		return isset($item->{"{$value}_image"});
	}

	public function createFile($name, $data = null) {
		$file = ItemImage::resizeAndSave($this->getValue(), $data, compact('name'));
		$item = $this->_item();

		if (ItemImage::$types[$value = $this->getValue()]['multiple']) {
			$images = $item->{"{$value}_images"} ? $item->{"{$value}_images"}->data() : array();

			if (!in_array($file->_id, $images)) {
				$images[] = $file->_id;
			}
			$item->{"{$value}_images"} = $images;
		} else {
			$item->{"{$value}_image"} = $file->_id;
		}
		return (boolean) $item->save();
	}

	protected function _item() {
		return Item::first(array(
			'conditions' => array('url' => $this->getParent()->getValue())
		));
	}
}

?>