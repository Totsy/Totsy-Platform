<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\ItemImageDirectory;
use admin\models\ItemImage;
use Sabre_DAV_Exception_FileNotFound;

class ItemDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function getChild($name) {
		if (!isset(ItemImage::$types[$name])) {
			throw new Sabre_DAV_Exception_FileNotFound("File `{$name}` not found,");
		}
		return new ItemImageDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach (ItemImage::$types as $name => $type) {
			$children[] = new ItemImageDirectory(array('value' => $name, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		return isset(ItemImage::$types[$name]);
	}
}

?>