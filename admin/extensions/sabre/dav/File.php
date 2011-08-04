<?php

namespace admin\extensions\sabre\dav;

use lithium\core\ConfigException;
use admin\models\File as FileModel;
use Sabre_DAV_Exception_Forbidden;
use Sabre_DAV_Exception_FileNotFound;

class File implements \Sabre_DAV_IFile {

	protected $_config = array();

	public function __construct(array $config = array()) {
		$defaults = array(
			'value' => null,
			'parent' => null
		);
		$this->_config = $config + $defaults;

		if (!isset($this->_config['value'])) {
			throw new ConfigException("Key `value` not specified.");
		}
	}

	public function __toString() {
		return $this->_config['value'];
	}

	public function getName() {
		return $this->__toString();
	}

	public function getValue() {
		return $this->_config['value'];
	}

	public function getParent() {
		return $this->_config['parent'];
	}

	/**
	 * Updates the data
	 *
	 * data is a readable stream resource.
	 *
	 * @param resource $data
	 * @return void
	 */
	public function put($data) {
		throw new Sabre_DAV_Exception_Forbidden('Permission denied to change data');
	}

	/**
	 * Returns the data
	 *
	 * This method may either return a string or a readable stream resource
	 *
	 * @return mixed
	 */
	public function get() {
		return $this->_file()->file->getBytes();
	}

	/**
	 * Returns the size of the file, in bytes.
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->_file()->file->getSize();
	}

	/**
	 * Returns the ETag for a file
	 *
	 * An ETag is a unique identifier representing the current version of the
	 * file. If the file changes, the ETag MUST change.  * The ETag is an
	 * arbritrary string, but MUST be surrounded by double-quotes.
	 *
	 * @return mixed Return null if the ETag can not effectively be determined
	 */
	public function getETag() {
		return $this->_file()->md5;
	}

	/**
	 * Returns the last modification time, as a unix timestamp
	 *
	 * @return int
	 */
	public function getLastModified() {
		return time();
	}

	/**
	 * Returns the mime-type for a file
	 *
	 * If null is returned, we'll assume application/octet-stream
	 */
	public function getContentType() {
		return null;
		// return File::mimeType($this->_file()->file->getBytes());
	}

	/**
	 * Deleted the current node
	 *
	 * @return void
	 */
	public function delete() {
		throw new Sabre_DAV_Exception_Forbidden('Permission denied to delete node');
	}

	/**
	 * Renames the node
	 *
	 * @param string $name The new name
	 * @return void
	 */
	public function setName($name) {
		throw new Sabre_DAV_Exception_Forbidden('Permission denied to rename file');
	}

	protected function _file($id) {
		return FileModel::find('first', array(
			'conditions' => compact('id')
		));
	}
}

?>