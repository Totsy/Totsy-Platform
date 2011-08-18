<?php

namespace li3_dav\controllers;

use lithium\core\Libraries;
use lithium\core\Environment;
use lithium\net\http\Router;
use Sabre_DAV_Server;
use Sabre_HTTP_Request;
use Sabre_DAV_Locks_Backend_File;
use Sabre_DAV_Locks_Plugin;
use Sabre_DAV_TemporaryFileFilterPlugin;

class FilesController extends \lithium\action\Controller {

	/**
	 * Provides a single point of entry for all DAV requests. This action
	 * configures any SabreDAV classes, plugins and custom VFS implementations.
	 * Any URLs below the route leading to this action are mapped/handled by
	 * SabreDAV.
	 *
	 * @return void
	 */
	public function dav() {
		Sabre_HTTP_Request::$defaultInputStream = $this->request->stream();

		$root = array();
		foreach (Libraries::get('li3_dav', 'tree') as $node) {
			$root[] = new $node();
		}
		$server = new Sabre_DAV_Server($root);

		$server->debugExceptions = !Environment::is('production');
		$server->setBaseUri(Router::match(array(
			'library' => 'li3_dav',
			'controller' => 'files', 'action' => 'dav',
			'token' => $this->request->token
		)));

		/* Filtering and locking are still using local files. */
		$resources = Libraries::get(true, 'resources');

		$backend = new Sabre_DAV_Locks_Backend_File($resources . '/dav/locks.dat');
		$plugin = new Sabre_DAV_Locks_Plugin($backend);
		$server->addPlugin($plugin);

		$plugin = new Sabre_DAV_TemporaryFileFilterPlugin($resources . '/dav/temporary');
		$server->addPlugin($plugin);

		$server->exec();
		exit;
	}
}

?>