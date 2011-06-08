<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use Sailthru_Util;
use Sailthru_Client;
use Sailthru_Client_Exception;

/**
 * Make a check for 0 records on the Disney Collection.
 * Redo the disney export at this specific date if it's the case
 */
class SailThru extends \lithium\console\Command {
	
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
		Sailthru_Client::send('Welcome Template','troyer@totsy.com');
	}
}