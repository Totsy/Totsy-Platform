<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\extensions\SailThru;

/**
 * Make a check for 0 records on the Disney Collection.
 * Redo the disney export at this specific date if it's the case
 */
class SailThruTest extends \lithium\console\Command {
	
	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';
	
	/**
	 * Instances
	 */
	public function run() {
		Environment::set($this->env);
		SailThru::send('Micah Test','troyer@totsy.com');
	}
}