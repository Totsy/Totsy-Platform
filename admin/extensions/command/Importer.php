<?php

namespace admin\extensions\command;

use lithium\core\Environment;
use lithium\analysis\Logger;


/**
 * The `Importer` command class contains core classes to capture files from remote sources.
 */
class Importer extends \lithium\console\Command {

	/**
	 * FTP Server of 3PL we are sending files to.
	 *
	 * @var string
	 */
	protected $_server = 'ftp.dotcomdistribution.com';

	/**
	 * FTP User Name.
	 *
	 * @var string
	 */
	protected $_user = 'TOT90';

	/**
	 * FTP Password.
	 *
	 * @var string
	 */
	protected $_password = '4J518t54';

	/**
	 * Connection to FTP Location.
	 */
	public $connection = null;

	/**
	 * Instances
	 */
	protected static $_instances = array();

	protected static function &_object() {
		$class = get_called_class();
		if (!isset(static::$_instances[$class])) {
			static::$_instances[$class] = new $class();
		}
		return static::$_instances[$class];
	}

	/**
	 * Get all the files from the remote path and write
	 * to the specified local location. After getting the file
	 */
	public static function getAll() {
		$self = static::_object();
		if ($self->connect()) {
			$self->changeDirectory('/tot90/out');
			$files = ftp_nlist($self->connection, ".");
			if ($files) {
				foreach ($files as $file) {
					if (substr($file, 0, 3) == 'CSH') {
						$localPath = "/totsy/shipfiles/$file";
						if ($self->getFile($localPath, $file)) {
							Logger::info("Downloaded $file to $localPath");
							$self->moveFile("/tot90/out/$file", "/tot90/out/bk/$file");
						}
					}
				}
			}
		}
		$self->disconnect();
	}

	/**
	 * Method to instantiate connection to an FTP location and login.
	 */
	public function connect() {
		$this->connection = ftp_connect($this->_server);
		if ($this->connection) {
			$this->link = $this->_login();
			Logger::info("Connected to $this->_server");
		} else {
			Logger::alert("Could not connect to $this->_server");
		}
		return $this->link;
	}

	/**
	 * Disconnect from the FTP connection.
	 */
	public function disconnect() {
		Logger::info("Closing connection to $this->_server");
		return ftp_close($this->connection);
	}

	/**
	 * Login to the connected FTP server.
	 */
	protected function _login() {
		try {
			$login = ftp_login($this->connection, $this->_user, $this->_password);
		} catch (Exception $e) {
			Logger::alert($e);
			Logger::alert("Authentication failed when connecting to $this->_server");
		}
		return $login;
	}

	/**
	 * Get files from the connected FTP Server.
	 */
	public function getFile($localFile, $remoteFile) {
		return ftp_get($this->connection, $localFile, $remoteFile, FTP_BINARY);
	}

	/**
	 * Put a file to the connected FTP Server.
	 */
	public function putFile($file, $path) {
		return ftp_put($this->connection, $file, $path, FTP_BINARY);
	}

	/**
	 * Move a file on the connected FTP Server.
	 */
	public function moveFile($old, $new) {
		Logger::info("Moving $old to $new on connection $this->_server");
		return ftp_rename($this->connection, $old, $new);
	}

	/**
	 * Change the directory
	 */
	public function changeDirectory($dir) {
		return ftp_chdir($this->connection, $dir);
	}
}