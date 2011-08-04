<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\ItemFile;
use admin\models\File;

class ItemDirectory extends \admin\extensions\sabre\dav\Directory {

	public function getChild($name) {
		$this->_model();
		$data = $model::find('first', array(
			'conditions' => $this->_conditions()
		));
		return new ItemFile(array('value' => $data->images[$name], 'parent' => $this));
	}

	public function getChildren() {
		$this->_model();
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
		$this->_model();
		$data = $model::find('first', array(
			'conditions' => $this->_conditions()
		));
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