<?php

namespace admin\models;

use lithium\data\Connections;

use admin\models\Event;
use admin\models\Item;

class File extends \lithium\data\Model {

	protected $_meta = array('source' => 'fs.files');

	/**
	 * Writes contents of an open handle to GridFS. Deduplication will take
	 * place as me data is detected to be already stored.
	 *
	 * @return object|boolean
	 */
	public static function write($handle, $meta = array()) {
		if ($dupe = File::dupe($handle)) {
			return $dupe;
		}

		/* As long as GridFS does not operate on file handles we've got
		   to dump the data into a variable first. */
		rewind($handle);
		$bytes = stream_get_contents($handle);

		if (!$id = static::getGridFS()->storeBytes($bytes)) {
			return false;
		}

		/* We'll need the complete document. */
		$file = File::first(array('conditions' => array('_id' => $id)));

		if ($meta) {
			$file->set($meta);
			$file->save();
		}
		return $file;
	}

	public static function getGridFS() {
		return static::_connection()->connection->getGridFS();
	}

	/**
	 * Searches for files already stored using a hash of given data.
	 *
	 * @param $data resource|string Either an already open handle, a string
	 *        with raw bytes or a string containing the path to a readable file.
	 * @return object Either the found dupe document or false.
	 */
	public static function dupe($data) {
		$close = false;

		if (is_string($data)) {
			/* Only handles we open inside here are also closed here. */
			$close = true;

			if (is_file($data)) {
				$handle = fopen($data, 'rb');
			} else {
				$handle = fopen('php://temp', 'w+b');
				fwrite($handle, $data);
			}
		} else {
			$handle = $data;
		}
		rewind($handle);

		$context = hash_init('md5');
		hash_update_stream($context, $handle);
		$hash = hash_final($context);

		if ($close) {
			fclose($handle);
		}
		return File::first(array('conditions' => array('md5' => $hash)));
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

	public static function mimeType($data) {
		$context = finfo_open(FILEINFO_NONE);

		rewind($data);
		$result = finfo_buffer($context, $data);

		finfo_close($context);
		return $result;
	}
}

?>