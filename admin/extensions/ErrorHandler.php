<?php

namespace admin\extensions;

use lithium\core\Environment;

class ErrorHandler extends \lithium\core\ErrorHandler {

	public static function __init() {
		parent::__init();

		static::$_checks += array(
			'env'  => function($config, $info) {
				var_dump(compact('config', 'info'));
				die('!!!!');
			}
		);
	}
}

?>