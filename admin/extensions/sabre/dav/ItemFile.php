<?php

namespace admin\extensions\sabre\dav;

use admin\models\File;

class ItemFile extends \admin\extensions\sabre\dav\File {

	public function put($data) {
		if ($file = $this->_file()) { /* This object represent an existing file. */
			$count = File::used($file->_id);

			/* This object is included in the count. */
			if ($count === 1) {
				$file->delete();
			}
		}
		$file = File::write($data);
		$item = $this->_item();

		$item->images = array(
			$this->getValue() => $file->_id
		) + $item->images->data();

		return $item->save();
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}

		if (File::used($file->_id) === 1) {
			$file->delete();
		}
		$item = $this->_item();

		$images = $item->images->data();
		unset($images[$this->getValue()]);
		$item->images = $images;

		return (boolean) $item->save();
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
				'_id' => $item->images[$this->getValue()]
			)
		));
	}
}

?>