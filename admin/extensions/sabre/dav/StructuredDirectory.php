<?php

namespace admin\extensions\sabre\dav;

use lithium\util\String;
use admin\models\File;
use MongoRegex;
use Exception;
use Sabre_DAV_Exception_Forbidden;
use Sabre_DAV_Exception_FileNotFound;


class StructuredDirectory implements \Sabre_DAV_ICollection {

	protected $_config = array();

	protected $_current;

	protected $_order = array(
		'model',
		'year',
		'month',
		'slug'
	);

	protected $_children = array();

	// /collection/year/month/slug/type.extension
	public function __construct(array $config = array()) {
		$defaults = array(
			'model' => null,
			'year' => null,
			'month' => null,
			'slug' => null,
			'type' => null,
			'current' => 'model'
		);
		$this->_config = $config + $defaults;

		if (!isset($this->_config['model'])) {
			throw new Exception("Model not specified.");
		}

		/*
		$this->_path = '/' . trim($path, '/');

		$regex  = '\/(?P<collection>[a-z]+)';
		$regex .= '\/(?P<year>[0-9]{4})';
		$regex .= '\/(?P<month>[0-9]{1,2})';
		$regex .= '\/(?P<slug>[a-z\-0-9]+)';
		$regex .= '\/(?P<type>[a-z]+)';
		$regex .= '\.(?P<extension>[a-z0-9]+)';

		if (!preg_match("/{$regex}/", $this->_path, $matches)) {
			throw new Exception("Path `{$this->_path}` doesn't match structure pattern.");
		}
		$this->_matched = $matches;
		*/
	}

	public function __toString() {
		$model = $this->_config['model'];

		return rtrim(String::insert('/{:source}/{:year}/{:month}', $this->_config + array(
			'source' => $model::meta('source')
		)), '/');
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
	public function createFile($name, $data = null) {}

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
	public function getChild($name) {
		// d('get child' . $name . ' /current ' . $this->_config['current']);

		if (!isset($this->_children[$name])) {
			throw new Sabre_DAV_Exception_FileNotFound('File not found: ' . $name);
		}
		// d('return child '. $name);
		return $this->_children[$name];

		$path = "{$this->_path}/{$path}";

// 		if (is_object($item)) {
// 			return new MongoFile($item);
// 		} else {
// 			$data = File::all(array(
// 				'conditions' => array(
// 					'paths' => new MongoRegex(
// 						'/^' . preg_quote($this->_path, '/') . '\/.*/'
// 					)
// 				)
// 			));
//
// 		}
	}

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre_DAV_INode[]
	 */
	public function getChildren() {
		$model = $this->_config['model'];
		$current = $this->_config['current'];
		$key = array_search($current, $this->_order) + 1;
		$next = isset($this->_order[$key]) ? $this->_order[$key] : false;

		switch ($current) {
			case 'model':
				$this->_children = array(
					2010 => new StructuredDirectory(array('year' => 2010, 'current' => $next) + $this->_config),
					2011 => new StructuredDirectory(array('year' => 2011, 'current' => $next) + $this->_config),
				);
			break;
			case 'year':
				$this->_children = array(
					1 => new StructuredDirectory(array('month' => 1, 'current' => $next) + $this->_config),
					2 => new StructuredDirectory(array('month' => 2, 'current' => $next) + $this->_config),
				);
			break;
			default:
				d('DEFAULT');
				break;
		}
		return $this->_children;

		/*
		$data = $model::all();
		foreach ($data as $item) {
			$children[] = new StructuredDirectory(compact('model') + array(
				'year' => $item->created_date
			));
		}*/
	}

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
	 * Returns the name of the node
	 *
	 * @return string
	 */
	public function getName() {
		return basename((string) $this);
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