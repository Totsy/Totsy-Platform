<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use admin\extensions\SailThru;

/**
 * Make a check for a normal transaction email.
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
	 * Email address to send
	 *
	 * @var string
	 */
	public 	$email = 'troyer@totsy.com';
	/**
	 * Sailthru Template to use.
	 *
	 * @var string
	 */
	public $template = 'Micah Test';	
	/**
	 * Instances
	 */
	public function run() {
		Environment::set($this->env);
		SailThru::send($this->template, $this->email);
	}
}