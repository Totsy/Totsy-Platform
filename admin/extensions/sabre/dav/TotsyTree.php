<?php

namespace admin\extensions\sabre\dav;

use admin\extensions\sabre\dav\FlatDirectory;
use admin\extensions\sabre\dav\StructuredDirectory;

class TotsyTree extends \Sabre_DAV_Tree {

	public function getNodeForPath($path) {
		// d($path);

		$path = explode('/', trim($path, '/'));
		$root = new RootDirectory();

		$root->children = array(
			'pending' => new FlatDirectory('pending'),
			'failed' => new FlatDirectory('failed'),
			'events' => new StructuredDirectory('events'),
			'items' => new StructuredDirectory('items')
		);

		if ($path) {
			return $tree->children[$path];
		} else {
			return $tree;
		}
		// throw new Sabre_DAV_Exception_FileNotFound('Could not find node at path: ' . $path);
	}
}

?>