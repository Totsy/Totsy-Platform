<?php

namespace admin\extensions\dav;

use lithium\core\ConfigException;
use admin\models\File;
use Sabre_DAV_Exception_Forbidden;
use Sabre_DAV_Exception_FileNotFound;
use Sabre_DAV_Exception_NotImplemented;

class GenericFile implements \Sabre_DAV_IFile {

	protected $_config = array();

	public function __construct(array $config = array()) {
		if (!isset($config['value'])) {
			throw new ConfigException("Key `value` not specified.");
		}
		$config['value'] = pathinfo($config['value'], PATHINFO_FILENAME);

		$defaults = array(
			'value' => null,
			'parent' => null
		);
		$this->_config = $config + $defaults;
	}

	public function __toString() {
		return $this->_config['value'];
	}

	public function getName() {
		$name = $this->_config['value'];

		if ($extension = $this->getExtension()) {
			$name .= ".{$extension}";
		}
		return $name;
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
		return (boolean) File::write($data);
	}

	/**
	 * Returns the data
	 *
	 * This method may either return a string or a readable stream resource
	 *
	 * @return mixed
	 */
	public function get() {
		if ($file = $this->_file()) {
			return $file->file->getBytes();
		}
	}

	/**
	 * Returns the size of the file, in bytes.
	 *
	 * @return int
	 */
	public function getSize() {
		if ($file = $this->_file()) {
			return $file->file->getSize();
		}
		return 0;
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
		if ($file = $this->_file()) {
			return '"' . $file->md5 . '"';
		}
	}

	/**
	 * Returns the last modification time, as a unix timestamp
	 *
	 * @return int
	 */
	public function getLastModified() {
		if ($file = $this->_file()) {
			if ($file->created_date) {
				/* Files in GridFS never get modified so we can just use the
				   created_date field. However some files may not even yet
				   feature that field. */
				return $file->created_date->sec;
			}
		}
		return time();
	}

	public function getExtension() {
		if ($file = $this->_file()) {
			return $file->extension();
		}
	}

	/**
	 * Returns the mime-type for a file
	 *
	 * If null is returned, we'll assume application/octet-stream
	 */
	public function getContentType() {
		if ($file = $this->_file()) {
			return $file->mimeType();
		}
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

	protected function _file() {
		throw new Sabre_DAV_Exception_NotImplemented('Method not implemented.');
	}
}

?>