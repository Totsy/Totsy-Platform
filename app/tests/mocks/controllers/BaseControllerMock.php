<?php

namespace app\tests\mocks\controllers;

class BaseControllerMock extends \app\controllers\BaseController {
	public function classes() {
		return $this->_classes;
	}

	public function renderData() {
		return $this->_render['data'];
	}
}

?>