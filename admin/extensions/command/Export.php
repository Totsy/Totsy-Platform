<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use MongoDate;
use MongoRegex;
use MongoId;
use admin\extensions\command\Base;
use admin\extensions\command\Exchanger;
use lithium\analysis\Logger;
use admin\extensions\util\String;

/**
 *
 */
class Export extends Base  {

	/**
	 * The environment to use when running the command. 'production' is the default.
	 * Set to 'development' or 'test' if you want to execute command on a different database.
	 *
	 * @var string
	 */
	public $env = 'development';

	/**
	 * The array list of events that should be batched
	 * processed.
	 *
	 * @var array
	 */
	protected $events = array();

	/**
	 * A summary of information that will be mailed to a group.
	 *
	 * @var array
	 */
	protected $summary = array();

	/**
	 * Any files that should be excluded during import.
	 *
	 * @var array
	 */
	protected $_exclude = array(
		'.',
		'..',
		'.DS_Store',
		'processed',
		'empty'
	);

	/**
	 * Allows verbose info logging. (default = false)
	 */
	public $verbose = true;

	/**
	 * Directory of files holding the files to FTP.
	 *
	 * @var string
	 */
	public $source = '/resources/totsy/pending/';

	/**
	 * Directory of files holding the backup files to FTP.
	 *
	 * @var string
	 */
	public $processed = '/resources/totsy/processed/';

	/**
	 * Full path to file.
	 */
	protected $path = null;

	public function run() {
		$this->source = LITHIUM_APP_PATH . $this->source;
		$this->processed = LITHIUM_APP_PATH . $this->processed;
		$this->_export();
	}
	/**
	 * This export script examine the source directory and send any files
	 * that have not already been transmitted. Once the transmission has been
	 * confirmed move the file over to a backup folder within the same directory.
	 *
	 */
	public function _export() {
		if ($this->source) {
			$handle = opendir($this->source);
			while (false !== ($this->file = readdir($handle))) {
				if (!(in_array($this->file, $this->_exclude))) {
					$fullPath = $this->source.$this->file;
					$backupPath = $this->processed.$this->file;
					if (filesize($fullPath) > 0) {
						if (Exchanger::putFile($this->file, $fullPath)) {
							$this->log("Successfully uploaded $this->file");
							$this->log("Moving $fullPath to $backupPath");
							rename($fullPath, $backupPath);
						} else {
							$this->error("There was a problem while uploading $this->file");
						}
					} else {
						$this->log("$fullPath was empty. Removing...");
						unlink($fullPath);
					}
				}
			}
		}
	}

}