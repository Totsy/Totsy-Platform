<?php

namespace app\extensions\command;

use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use lithium\analysis\Logger;


/**
 * The `Base` class is a collection of methods that are useful to other commands.
 */
class Base extends \lithium\console\Command {

	public function log($message, $type = 'info') {
		if ($this->verbose) {
			$class = get_class($this);
			Logger::$type("Li3 $class: " . $message);
		}
	}
}