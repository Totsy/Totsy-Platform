<?php

namespace admin\controllers;

use lithium\core\Libraries;
use lithium\core\Environment;
use Sabre_DAV_Server;
use Sabre_DAV_FS_Directory;
use Sabre_DAV_Locks_Backend_File;
use Sabre_DAV_Locks_Plugin;
use Sabre_DAV_TemporaryFileFilterPlugin;

class DavController extends \lithium\action\Controller {

	public function handle() {
		$resources = Libraries::get('admin', 'resources');

		// @todo This is temporary and will be replaced by an implementation
		//       storing files directly in GridFS.
		$root = new Sabre_DAV_FS_Directory($resources . '/dav/share');

		$server = new Sabre_DAV_Server($root);

		$server->debugExceptions = !Environment::is('production');
		$server->setBaseUri('/dav');


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