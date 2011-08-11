<?php

namespace admin\models;

use \lithium\data\Connections;
use \Imagine\Gd\Imagine;
use \Imagine\Image\Box;

class EventImage extends File {

	protected $_meta = array("source" => "fs.files");

	/**
	 * Image sizes for event images. With associated dimensions width and
	 * height. The names correspond to the keys of an event document's `images`
	 * array with the expception that `_image` is appended.
	 *
	 * @var types array
	 */
	public static $types = array(
		'event'        => array('dimensions' => array(300, 193)),
		'logo'         => array('dimensions' => array(148, 52)),
		'splash_big'   => array('dimensions' =>  array(355, 410)),
		'splash_small' => array('dimensions' => array(298, 344))
	);

	/*
	 *
	 * @param string $position The event image position (event, logo, splash_big, etc.)
	 * @param array $data The file data array from the POST data - a single file or raw bytes.
	 * @return
	*/
	public static function resizeAndSave($position, $data, $meta = array()) {
		if (empty($data) || !isset(static::$types[$position])) {
			return false;
		}
		list($width, $height) = static::$types[$position]['dimensions'];

		$imagine = new Imagine();

		if (is_array($data) && isset($data['tmp_file'])) { /* fileupload */
			$image = $imagine->open($data['tmp_file']);
		} elseif (is_string($data)) { /* bytes */
			$image = $imagine->load($data);
		} elseif (is_resource($data)) {
			rewind($data);
			$image = $imagine->load(stream_get_contents($data));
		} else {
			return false;
		}
		$bytes = $image->resize(new Box($width, $height))->get('png');

		// Write the image to GridFS
		// Return what should be the file object that write() returns... this will have an id to associate
		return static::write($bytes, $meta + array('mime_type' => 'image/png'));
	}
}

?>