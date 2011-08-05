<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\ItemFile;
use admin\models\File;
use Sabre_DAV_Exception_FileNotFound;

class ItemDirectory extends \admin\extensions\sabre\dav\Directory {

	public function getChild($name) {
		$model = $this->_model();
		$data = $model::find('first', array(
			'conditions' => $this->_conditions()
		));
		if (!isset($data->images[$name])) {
			throw new Sabre_DAV_Exception_FileNotFound("File `{$name}` not found,");
		}
		return new ItemFile(array('value' => $name, 'parent' => $this));
	}

	public function getChildren() {
		$model = $this->_model();
		$data = $model::find('first', array(
			'conditions' => $this->_conditions()
		));

		$children = array();
		foreach ($data->images as $name => $id) {
			$children[] = new ItemFile(array('value' => $name, 'parent' => $this));
		}
		return $children;
	}

	public function childExists($name) {
		$model = $this->_model();
		$data = $model::find('first', array(
			'conditions' => $this->_conditions()
		));

		$name = pathinfo($name, PATHINFO_FILENAME);
		return isset($data->images[$name]);
	}

	protected function _conditions() {
		return array(
			'url' => $this->getValue()
		);
	}

	protected function _model() {
		return $this->getParent()->getParent()->getParent()->getValue();
	}
}

?>