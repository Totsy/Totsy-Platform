<?php

namespace admin\extensions\sabre\dav;

use admin\models\File;
use admin\extensions\sabre\dav\GenericFile;
use Exception;
use Sabre_DAV_Exception_FileNotFound;

class OrphanedDirectory extends \admin\extensions\sabre\dav\GenericDirectory {

	public function __construct(array $config = array()) {
		parent::__construct($config + array('value' => 'orphaned'));
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
		return new OrphanedFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$children = array();

		foreach (File::orphaned() as $item) {
			$children[] = new OrphanedFile(array('value' => (string) $item->_id, 'parent' => $this));
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
}

?>