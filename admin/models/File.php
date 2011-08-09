<?php

namespace admin\models;

use lithium\data\Connections;

use admin\models\Event;
use admin\models\Item;
use MongoDate;

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

		$meta += array(
			'created_date' => new MongoDate(),
			'mime_type' => static::mimeType($handle)
		);
		$file->set($meta);
		$file->save();

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

		$meta = stream_get_meta_data($handle);
		if ($meta['seekable']) {
			rewind($handle);
		}

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

	// @todo replace with map reduce
	public static function pending() {
		$data = static::all();
		$results = array();

		foreach ($data as $item) {
			if (!static::used($item->_id)) {
				$results[] = $item;
			}
		}
		return $results;
	}

	public static function mimeType($data) {
		$context = finfo_open(FILEINFO_MIME);

		rewind($data);
		$peekBytes = 1000000;
		$result = finfo_buffer($context, fgets($data, $peekBytes));
		list($type, $attributes) = explode(';', $result, 2) + array(null, null);

		finfo_close($context);
		return $type;
	}
}

/**
 * Ensure only unused files can be deleted.
 */
File::applyFilter('delete', function($self, $params, $chain) {
	if (File::used($params['entity']->_id)) {
		return false;
	}
	return $chain->next($self, $params, $chain);
});

?>