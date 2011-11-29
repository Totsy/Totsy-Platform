<?php

namespace app\models;

use lithium\data\Connections;


class File extends \lithium\data\Model {

	protected $_meta = array("source" => "fs.files");
		
	public function getGridFS() {
		$collection = File::_connection()->connection;
		return $collection->getGridFS();
	}
	
}


?>