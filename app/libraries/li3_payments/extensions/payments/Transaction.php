<?php

namespace li3_payments\extensions\payments;

class Transaction extends \lithium\core\Object {

	const TYPE_VOID = 'void';

	const TYPE_AUTH = 'auth';

	const TYPE_CAPTURE = 'capture';

	const TYPE_AUTH_CAPTURE = 'authcapture';

	const TYPE_CREDIT = 'credit';

	protected function _init() {
		parent::_init();

		foreach ($this->_config as $key => $val) {
			$this->{$key} = $val;
		}
	}
}

?>