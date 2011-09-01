<?php

namespace admin\extensions\command;

use lithium\core\Libraries;

/**
 * Show messages from a log file
 */
class Log extends \lithium\console\Command {

	/**
	 * The path in which to search for log files. Defaults to "<app>/resources/tmp/logs".
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The line number to retrieve from the file. If empty, the last line is retrieved.
	 *
	 * @var integer
	 */
	public $line;

	protected function _init() {
		parent::_init();
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$this->path = $this->path ?: Libraries::get(true, 'resources') . '/tmp/logs';
		$this->line = $this->line ? intval($this->line) : null;
	}

	public function show() {
		if (!$this->request->params['args']) {
			$this->out();
			$this->out("Please specify a log file to show:");
			$this->out("\t" . "- " . join("\n\t- ", $this->_logs()));
			$this->out();
			return true;
		}
		$log = array_shift($this->request->params['args']);

		if (!in_array($log, $this->_logs())) {
			$this->out("Invalid log file selected.");
			return false;
		}
		$this->_show($log);
	}

	/**
	 * Fetches and displays a line number from a log file, defaulting to the last line in the file
	 *
	 * @return void
	 */
	protected function _show($log) {
		$i = 0;
		$file = fopen("{$this->path}/{$log}.log", "r");

		while (!feof($file)) {
			$i++;

			if ($tmp = trim(fgets($file))) {
				$line = $tmp;
			}

			if ($this->line == $i) {
				break;
			}
		}
		list($date, $time, $data) = explode(' ', $line, 3);
		$data = json_decode($data, true);
		$trace = array_shift($data['stack']);

		$this->out();
		$this->out("{$date} {$time}: {$data['message']}");
		$this->out("{$trace} @ {$data['line']} ({$data['file']})");
		$this->out();
		$this->out('Stack trace:');
		$this->out(' - ' . join("\n - ", $data['stack']) . "\n");
	}

	/**
	 * Lists the available log files.
	 *
	 * @return array Returns an array of log file names.
	 */
	protected function _logs() {
		$trim = function($file) { return preg_replace('/\.log$/', '', $file); };
		return array_map($trim, preg_grep('/\.log$/', scandir($this->path)));
	}
}

?>