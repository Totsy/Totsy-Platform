<?php

namespace admin\extensions\dav;

use admin\models\File;
use admin\extensions\dav\GenericFile;
use Exception;
use Sabre_DAV_Exception_FileNotFound;

class PendingDirectory extends \admin\extensions\dav\GenericDirectory {

	public function __construct(array $config = array()) {
		parent::__construct($config + array('value' => 'pending'));
	}

	public function getChild($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);
		$item = File::first(array(
			'conditions' => array(
				'_id' => $name
			)
		));
		if (!$item) {
			throw new Sabre_DAV_Exception_FileNotFound("File `{$name}` not found,");
		}
		return new PendingFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach (File::pending() as $item) {
			$children[] = new PendingFile(array('value' => (string) $item->_id, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$name = pathinfo($name, PATHINFO_FILENAME);

		// @todo must also check if is pending
		$item = File::first(array(
			'conditions' => array(
				'_id' => $name
			)
		));
		return (boolean) $item;
	}

	public function createFile($name, $data = null) {
		return (boolean) File::write($data, compact('name') + array('pending' => true));
	}
}

?>