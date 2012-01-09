<?php

namespace app\tests\mocks\storage\session\adapter;

class MemoryMock extends \lithium\storage\session\adapter\Memory {

	public static function key() {
		return 'session key';
	}
}

?>