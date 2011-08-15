<?php

namespace admin\models;

use lithium\data\Connections;
use lithium\net\http\Media;
use admin\models\Event;
use admin\models\Item;
use MongoDate;
use Imagine\Gd\Imagine;

class File extends \lithium\data\Model {

	protected $_meta = array('source' => 'fs.files');

	/**
	 * Writes contents of an open handle to GridFS. Deduplication will take
	 * place as me data is detected to be already stored.
	 *
	 * @param $data resource|string Either an already open handle, a string
	 *        with raw bytes or a string containing the path to a readable file.
	 * @return object|boolean
	 */
	public static function write($data, $meta = array()) {
		/* Normalize $data */
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

		/* Dupe detection */
		if ($dupe = File::_dupe($handle)) {
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
			'mime_type' => static::detectMimeType($handle),
			'dimensions' => static::detectDimensions($bytes)
		);

		if ($close) {
			fclose($handle);
		}
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
	 * @param $handle resource An already open handle.
	 * @return object Either the found dupe document or false.
	 */
	protected static function _dupe($handle) {
		rewind($handle);

		$context = hash_init('md5');
		hash_update_stream($context, $handle);
		$hash = hash_final($context);

		return File::first(array('conditions' => array('md5' => $hash)));
	}

	public static function used($id) {
		$result = Event::all(array(
			'conditions' => array(
				'images.event_image' => $id,
				'images.logo_image' => $id,
				'images.splash_big_image' => $id,
				'images.splash_small_image' => $id
			)
		));
		if ($result->count()) {
			return true;
		}
		$result = Item::all(array(
			'conditions' => array(
				'alternate_images' => $id,
				'primary_image' => $id,
				'zoom_image' => $id,
				'cart_image' => $id,
				'event_thumbnail_image' => $id
			)
		));

		return (boolean) $result->count();
	}

	public static function pending() {
		return static::all(array('conditions' => array('pending' => true)));
	}

	// @todo replace with map reduce
	public static function orphaned() {
		$data = static::all(array('conditions' => array('pending' => false)));
		$results = array();

		foreach ($data as $item) {
			if (!static::used($item->_id)) {
				$results[] = $item;
			}
		}
		return $results;
	}

	public function mimeType($entity) {
		if ($entity->mime_type) {
			return $entity->mime_type;
		}
		/* Some files in GridFS may not yet have a `mime_type` field.
		   This field was added later so the code segment below
		   provides BC for that. */

		return static::detectMimeType($entity->file->getBytes());
	}

	public function dimensions($entity) {
		if ($entity->dimensions) {
			return $entity->dimensions->data();
		}
		return static::detectDimensions($entity->file->getBytes());
	}

	public function basename($entity) {
		$name = $entity->_id;

		if ($extension = $entity->extension()) {
			$name .= ".{$extension}";
		}
		return $name;
	}

	public function extension($entity) {
		return Media::type($entity->mimeType());
	}

	public function url($entity) {
		return '/image/' . $entity->basename();
	}

	public static function detectMimeType($data) {
		$context = finfo_open(FILEINFO_MIME);

		if (is_resource($data)) {
			rewind($data);
			$peekBytes = 1000000;
			$result = finfo_buffer($context, fgets($data, $peekBytes));
		} else {
			$result = finfo_buffer($context, $data);
		}
		list($type, $attributes) = explode(';', $result, 2) + array(null, null);

		finfo_close($context);
		return $type;
	}

	public static function detectDimensions($data) {
		$imagine = new Imagine();
		$box = $imagine->load($data)->getSize();

		return array(
			'width' => $box->getWidth(),
			'height' => $box->getHeight()
		);
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