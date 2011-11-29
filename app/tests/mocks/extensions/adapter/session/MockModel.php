<?php

namespace app\tests\mocks\extensions\adapter\session;

class MockModel extends \app\extensions\adapter\session\Model {
	public function data() {
		return $this->_data;
	}
}

?>