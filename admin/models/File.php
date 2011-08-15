<?php

namespace admin\models;

use lithium\data\Connections;
use lithium\net\http\Media;
use admin\models\Event;
use admin\models\Item;
use MongoDate;
use MongoCode;
use Imagine\Gd\Imagine;

class File extends \lithium\data\Model {

	protected $_meta = array('source' => 'fs.files');
	/**
	 * Enable/disable deduping.
	 *
	 * @see admin\models\File::write()
	 * @var boolean
	 */
	public static $dedupe = true;

	/**
	 * Writes contents of an open handle to GridFS. Deduplication will take
	 * place as me data is detected to be already stored.
	 *
	 * @param $data resource|string Either an already open handle, a string
	 *        with raw bytes or a string containing the path to a readable file.
	 * @return object|boolean
	 */
	public static function write($data, $meta = array(), array $options = array()) {
		$options += array(
			'dedupe' => true
		);

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
			$handle = static::_upgrade($data);
			$close = $handle != $data;
		}

		/* Dupe detection */
<<<<<<< HEAD
		if (static::$dedupe && ($dupe = static::_dupe($handle))) {
=======
		if ($options['dedupe'] && ($dupe = static::_dupe($handle))) {
>>>>>>> Adding option to disable deduping for testing purposes.
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

		return static::first(array('conditions' => array('md5' => $hash)));
	}

	public static function used($id) {
		$result = Event::all(array(
			'conditions' => array(
				'$or' => array(
					array('images.event_image' => $id),
					array('images.logo_image' => $id),
					array('images.splash_big_image' => $id),
					array('images.splash_small_image' => $id),
				)
			)
		));
		if ($result->count()) {
			return true;
		}

		$result = Item::all(array(
			'conditions' => array(
				'$or' => array(
					array('alternate_images' => $id),
					array('primary_image' => $id),
					array('zoom_image' => $id),
					array('cart_image' => $id),
					array('event_thumbnail_image' => $id)
				)
			)
		));
		return (boolean) $result->count();
	}

	public static function pending($conditions = array()) {
		return static::all(array('conditions' => array('pending' => true) + $conditions));
	}
	/**
	 * Retrieves all files flagged as orphaned. As detecting orphans is an
	 * expensive task flagging needs to happen through running a command.
	 *
	 * @see admin\extensions\command\FileOrphan
	 */
	public static function orphaned() {
		return static::all(array('conditions' => array('orphaned' => true)));
	}

	public function rename($entity, $name) {
		/* Mistakenly pasted tags. */
		$name = strip_tags($name);

		/* Names may contain slashes which confuse PHP's filename parsing
		   mechanisms. However slashes are allowed as we're storing in
		   GridFS. Also: Escape any other charactes that are valid i.e. Vendor
		   Styles but not files. */
		$escape = array('/' => '-+-');
		$name = strtr($name, $escape);

		$name = pathinfo($name, PATHINFO_FILENAME);
		$extension = $entity->extension(array('quick' => false));

		if ($extension) {
			$name .= ".{$extension}";
		}
		$name = strtr($name, array_flip($escape));

		$entity->name = $entity->file->name = $name;

		return $entity;
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

	public function basename($entity) {
		$name = $entity->_id;

		if ($extension = $entity->extension()) {
			$name .= ".{$extension}";
		}
		return $name;
	}

	public function extension($entity, array $options = array()) {
		$defaults = array('quick' => true);
		$options += $defaults;

		if ($options['quick']) {
			if ($result = strtolower(pathinfo($entity->name, PATHINFO_EXTENSION))) {
				return $result;
			}
		}
		$result = Media::type($entity->mimeType());

		if (is_array($result)) {
			return current($result);
		}
		return $result;
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

		if ($type != 'application/x-empty') {
			return $type;
		}
	}
	protected static function _upgrade($stream) {
		$meta = stream_get_meta_data($stream);

		if ($meta['seekable']) {
			return $stream;
		}
		$upgrade = fopen('php://temp', 'w+b');
		stream_copy_to_stream($stream, $upgrade);

		return $upgrade;
	}

/**
 * Ensure only unused files can be deleted.
 */
File::applyFilter('delete', function($self, $params, $chain) {
	if (File::$dedupe && File::used($params['entity']->_id)) {
		return false;
	}
	return $chain->next($self, $params, $chain);
});

?>