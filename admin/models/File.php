<?php

namespace admin\models;

use lithium\data\Connections;

use admin\models\Event;
use admin\models\Item;

class File extends \lithium\data\Model {

	protected $_meta = array('source' => 'fs.files');

	public static function getGridFS() {
		return static::_connection()->connection->getGridFS();
	}

	public static function dupe($data) {
		rewind($data);

		$context = hash_init('md5');
		hash_update_stream($context, $data);
		$hash = hash_final($context);

		return File::first(array(
			'conditions' => array('md5' => $hash)
		));
	}

	public static function used($id) {
		$count  = Event::count(array(
			'conditions' => array(
				'images' => $id
			)
		));
		$count += Item::count(array(
			'conditions' => array(
				'images' => $id
			)
		));
		return $count;
	}

	public static mimeType($data) {
		$context = finfo_open(FILEINFO_NONE);

		rewind($data);
		$result = finfo_buffer($context, $data);

		finfo_close($context);
		return $result;
	}
}

?>