<?php

namespace app\tests\mocks\models;

class MockSessionEntity extends \lithium\data\Entity {
	public $deleted = false;
	public $saved = false;

	protected $_model = 'app\tests\mocks\models\MockSessionModel';

	public function delete($options = array ()) {
		$this->deleted = true;
		return true;
	}

	public function save($data = null, $options = array()) {
		$this->set((array) $data);
		$this->saved = true;
		return true;
	}
}

?>