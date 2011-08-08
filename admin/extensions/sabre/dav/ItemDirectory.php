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

		$name = pathinfo($name, PATHINFO_FILENAME);
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

	public function createFile($name, $data = null) {
		$model = $this->_model();

		$file = File::write($data, compact('name'));
		$name = pathinfo($name, PATHINFO_FILENAME);

		$item = $model::find('first', array(
			'conditions' => $this->_conditions()
		));
		$item->images = array(
			$name => $file->_id
		) + $item->images->data();

		return (boolean) $item->save();
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