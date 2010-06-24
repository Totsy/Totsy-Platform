<?php

namespace admin\models;

use \lithium\data\Connections;


class File extends \lithium\data\Model {

	public function getGridFS() {
		$collection = File::_connection()->connection;
		return $collection->getGridFS();
	}
	
}


?>