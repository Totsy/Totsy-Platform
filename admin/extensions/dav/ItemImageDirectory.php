<?php

namespace admin\extensions\dav;

use admin\extensions\dav\ItemFile;
use admin\models\Event;
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
		$position = $this->getValue();
		$type = ItemImage::$types[$position];

		$children = array();

		if (!$item->{$type['field']}) {
			return $children;
		}
		if ($type['multiple']) {
			foreach ($item->{$type['field']} as $id) {
				$children[] = new ItemFile(array('value' => $id, 'parent' => $this));
			}
		} else {
			$id = $item->{$type['field']};
			$children[] = new ItemFile(array('value' => $id, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		$item = $this->_item();
		$position = $this->getValue();
		$type = ItemImage::$types[$position];

		if (!$item->{$type['field']}) {
			return false;
		}
		if ($type['multiple']) {
			return in_array($name, $item->{$type['field']}->data());
		}
		return isset($item->{$type['field']});
	}

	public function createFile($name, $data = null) {
		$position = $this->getValue();
		$item = $this->_item();

		$file = ItemImage::resizeAndSave($position, $data, compact('name'));
		$item->attachImage($position, $file->_id);

		return true;
	}

	protected function _item() {
		/* Gets value from EventDirectory. */
		$url = $this->getParent()->getParent()->getParent()->getValue();
		$id = Event::first(array('conditions' => compact('url')))->_id;
		return Item::first(array(
			'conditions' => array(
				'vendor_style' => $this->getParent()->getValue(),
				'event' => (string) $id
			)
		));
	}
}

?>