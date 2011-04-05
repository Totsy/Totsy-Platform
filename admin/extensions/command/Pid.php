<?php

namespace admin\extensions\command;

/**
 * Check to see if a process is already runnging.
 */
class Pid extends \lithium\console\Command  {

	/**
	 * Full path of temporary filename
	 * @var string
	 */
	protected $filename;

	/**
	 * Flag set for running process.
	 * @var boolean
	 */
	public $already_running = false;
   
	function __construct($directory, $file) {
		$this->filename = $directory . '/' . $file . '.pid';
		if (is_writable($this->filename) || is_writable($directory)) {
			if(file_exists($this->filename)) {
				$pid = (int) trim(file_get_contents($this->filename));
				$this->already_running = true;
			}
		} else {
			die("Cannot write to pid file '$this->filename'. Program execution halted.\n");
		}
		if (!$this->already_running) {
			$pid = getmypid();
			file_put_contents($this->filename, $pid);
		}
    }

    public function __destruct() {
		if (!$this->already_running && file_exists($this->filename) && is_writable($this->filename)) {
			unlink($this->filename);
		}
    }

}