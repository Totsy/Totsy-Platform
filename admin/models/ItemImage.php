<?php

namespace admin\models;

use \lithium\data\Connections;
use \Imagine\Gd\Imagine;
use \Imagine\Image\Box;

class ItemImage extends File {

	protected $_meta = array("source" => "fs.files");

	/**
	 * Image sizes for event images. With associated dimensions width and
	 * height. The names correspond to the keys of an item document's
	 * `alternate_images`, `zoom_image`, `primary_image` fields with the
	 * expception that `_image(s)` is appended.
	 *
	 * @var types array
	 */
	public static $types = array(
		'primary' => array(
			'dimensions' => array(298, 300),
			'field' => 'primary_image',
			'multiple' => false
		),
		'zoom' => array(
			'dimensions' => array(596, 600),
			'field' => 'zoom_image',
			'multiple' => false
		),
		'alternate' => array(
			'dimensions' => array(298, 300),
			'field' => 'alternate_images',
			'multiple' => true
		),
		'cart' => array(
			'dimensions' => array(60, 60),
			'field' => 'cart_image',
			'multiple' => false
		),
		'event_thumbnail' => array(
			'dimensions' => array(93, 93),
			'field' => 'event_thumbnail_image',
			'multiple' => false
		)
	);

	/*
	 *
	 * @param string $position The item image position (primary, zoom, etc.)
	 * @param array $data The file data array from the POST data - a single file
	 * @return
	*/
	public static function resizeAndSave($position, $data, $meta = array()) {
		if (empty($data) || !isset(static::$types[$position])) {
			return false;
		}
		list($width, $height) = static::$types[$position]['dimensions'];

		$imagine = new Imagine();
		$filename = null;

		if (is_array($data) && isset($data['tmp_name'])) { /* fileupload */
			$image = $imagine->open($data['tmp_name']);
			$filename = $data['name'];
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
		return static::write($bytes, $meta + array('name' => $filename, 'mime_type' => 'image/png'));
	}
}

?>