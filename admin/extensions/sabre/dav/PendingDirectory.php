<?php

namespace admin\extensions\sabre\dav;

use admin\models\File as FileModel;
use admin\extensions\sabre\dav\File;
use Exception;
use Sabre_DAV_Exception_FileNotFound;

class PendingDirectory extends \admin\extensions\sabre\dav\Directory {

	public function __construct(array $config = array()) {
		parent::__construct($config + array('value' => 'pending'));
	}

	public function getChild($name) {
		$item = FileModel::first(array(
			'conditions' => array(
				'name' => $name
			)
		));
		if (!$item) {
			throw new Sabre_DAV_Exception_FileNotFound("File `{$name}` not found,");
		}
		return new File(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach (FileModel::pending() as $item) {
			$children[] = new File(array('value' => $item->name, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		// @todo must also check if is pending
		$item = FileModel::first(array(
			'conditions' => array(
				'name' => $name
			)
		));
		return (boolean) $item;
	}

	public function createFile($name, $data = null) {
		return (boolean) FileModel::write($data, compact('name'));
	}
}

?>