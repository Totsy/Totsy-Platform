<?php

namespace admin\controllers;

use lithium\core\Libraries;
use Sabre_DAV_Server;
use Sabre_DAV_FS_Directory;
use Sabre_DAV_Locks_Backend_File;
use Sabre_DAV_Locks_Plugin;

class DavController extends \lithium\action\Controller {

	public function handle() {
		$resources = Libraries::get('app', 'resources');

		// @todo This is temporary and will be replaced by an implementation
		//       storing files directly in GridFS.
		$root = new Sabre_DAV_FS_Directory($resources . '/tmp');

		$server = new Sabre_DAV_Server($root);
		$server->setBaseUri('/dav');

		$lockBackend = new Sabre_DAV_Locks_Backend_File($resources . '/dav_locks');
		$lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);

		$server->addPlugin($lockPlugin);

		$server->exec();
		exit;
	}
}

?>