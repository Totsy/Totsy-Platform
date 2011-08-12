<?php

namespace admin\extensions\sabre\dav;

use admin\models\File;
use Sabre_DAV_Exception_Forbidden;

class PendingFile extends \admin\extensions\sabre\dav\GenericFile {

	public function put($data) {
		return (boolean) File::write($data, array('pending' => true));
	}

	public function delete() {
		if (!$file = $this->_file()) {
			return;
		}
		if (!$file->delete()) {
			throw new Sabre_DAV_Exception_Forbidden('Permission denied to delete node');
		}
		return true;
	}

	protected function _file() {
		return File::first(array(
			'conditions' => array(
				'_id' => $this->getValue()
			)
		));
	}
}

?>