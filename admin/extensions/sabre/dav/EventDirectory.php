<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\EventImageDirectory;
use admin\models\EventImage;
use Sabre_DAV_Exception_FileNotFound;

class EventDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function getChild($name) {
		if (!isset(EventImage::$types[$name])) {
			throw new Sabre_DAV_Exception_FileNotFound("File `{$name}` not found,");
		}
		return new EventImageDirectory(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();
		foreach (EventImage::$types as $name => $type) {
			$children[] = new EventImageDirectory(array('value' => $name, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		return isset(EventImage::$types[$name]);
	}
}

?>