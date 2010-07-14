<?php

namespace admin\models;

use \lithium\data\Connections;


class File extends \lithium\data\Model {


	protected $_meta = array("source" => "fs.files");
	
	public static function getGridFS() {
		$collection = static::_connection()->connection;
		return $collection->getGridFS();
	}
}


?>