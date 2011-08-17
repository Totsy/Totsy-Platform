<?php

namespace admin\extensions\dav;

use admin\extensions\dav\ItemFile;
use admin\models\Item;
use admin\models\ItemImage;
use Sabre_DAV_Exception_FileNotFound;

class ItemImageDirectory extends \admin\extensions\dav\GenericDirectory {

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
		$position = $this->getValue();
		$file = ItemImage::resizeAndSave($position, $data, compact('name'));
		$item = $this->_item();

		$item->attachImage($position, $file->_id);
		return $item->save(null, Item::imagesWhitelist());
	}

	protected function _item() {
		return Item::first(array(
			'conditions' => array('url' => $this->getParent()->getValue())
		));
	}
}

?>