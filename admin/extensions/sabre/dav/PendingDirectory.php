<?php

namespace admin\extensions\sabre\dav;

use admin\models\File;
use admin\extensions\sabre\dav\GenericFile;
use Exception;
use Sabre_DAV_Exception_FileNotFound;

class PendingDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function __construct(array $config = array()) {
		parent::__construct($config + array('value' => 'pending'));
	}

	public function getChild($name) {
		$item = File::first(array(
			'conditions' => array(
				'name' => $name
			)
		));
		if (!$item) {
			throw new Sabre_DAV_Exception_FileNotFound("File `{$name}` not found,");
		}
		return new GenericFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach (File::pending() as $item) {
			$children[] = new GenericFile(array('value' => $item->name, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		// @todo must also check if is pending
		$item = File::first(array(
			'conditions' => array(
				'name' => $name
			)
		));
		return (boolean) $item;
	}

	public function createFile($name, $data = null) {
		return (boolean) File::write($data, compact('name'));
	}
}

?>