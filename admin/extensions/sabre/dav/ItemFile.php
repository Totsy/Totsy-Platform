<?php

namespace admin\extensions\sabre\dav;

use admin\models\File;

class ItemFile extends \admin\extensions\sabre\dav\File {

	public function put($data) {
		if ($file = $this->_file()) {
			$count = File::used($file->_id)

			/* The current item this file is attached to is included in the count. */
			if ($count === 1) {
				$file->delete();
			}
		}
		if ($file = File::dupe($data)) {
			$id = $file->_id;
		} else {
			$grid = File::getGridFS();

			rewind($data);
			$id = $grid->storeBytes($data);
			$file = File::first(array('conditions' => array('_id' => $id)));
		}

		$item = $this->_item();
		$item->images[$this->getValue()] = $id;

		return $item->save();
	}

	public function delete() {
		$file = $this->_file();

		if (File::used($file->_id) === 1) {
			$file->delete();
		}
		$item = $this->_item();
		unset($item->images[$this->getValue()]);

		return $item->save();
	}

	protected function _model() {
		return $this->getParent()->getParent()->getParent()->getParent()->getValue();
	}

	protected function _item() {
		$model = $this->_model();

		return $model::find('first', array(
			'conditions' => array(
				'url' => $this->getParent()->getValue()
			)
		));
	}

	protected function _file() {
		$item = $this->_item();

		return File::find('first', array(
			'conditions' => array(
				'id' => $item->images[$this->getValue()]
			)
		));
	}
}

?>