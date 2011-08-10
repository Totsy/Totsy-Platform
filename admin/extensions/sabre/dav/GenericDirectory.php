<?php

namespace admin\extensions\sabre\dav;

use lithium\core\ConfigException;
use Sabre_DAV_Exception_Forbidden;
use Sabre_DAV_Exception_FileNotFound;

class GenericDirectory implements \Sabre_DAV_ICollection {

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
	 * Creates a new file in the directory
	 *
	 * data is a readable stream resource
	 *
	 * @param string $name Name of the file
	 * @param resource $data Initial payload
	 * @return void
	 */
	public function createFile($name, $data = null) {
		throw new Sabre_DAV_Exception_Forbidden('Permission denied to create file');
	}

	/**
	 * We block creating directories here as users are allowed to drop files
	 * only into existing directorries.
	 *
	 * @param string $name
	 * @return void
	 */
	public function createDirectory($name) {
		throw new Sabre_DAV_Exception_Forbidden('Permission denied to create directory');
	}

	/**
	 * Returns a specific child node, referenced by its name
	 *
	 * @param string $name
	 * @return Sabre_DAV_INode
	 */
	public function getChild($name) {}

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre_DAV_INode[]
	 */
	public function getChildren() {}

	/**
	 * Checks if a child-node with the specified name exists
	 *
	 * @return bool
	 */
	public function childExists($name) {
		return false;
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

	/**
	 * Returns the last modification time, as a unix timestamp
	 *
	 * @return int
	 */
	public function getLastModified() {
		return time();
	}
}

?>