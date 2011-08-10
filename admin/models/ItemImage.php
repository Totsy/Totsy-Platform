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
		'primary'   => array('dimensions' => array(300, 300)),
		// 'zoom'      => array('dimensions' => array()),
		// 'alternate' => array('dimensions' => array())
	);

	/*
	 * 
	 * @param string $position The item image position (primary, zoom, etc.)
	 * @param array $data The file data array from the POST data - a single file
	 * @return 
	*/
	public static function resizeAndSave($position=null, $data=null) {
		if(empty($data) || !isset(static::$types[$position])) {
			return false;
		}
		list($width, $height) = static::$types[$position]['dimensions'];

		// Resize the image
		$tmp_file = (isset($data['tmp_name'])) ? $data['tmp_name']:null;
		$imagine = new Imagine();
		$image = $imagine->open($tmp_file);
		$resized_image_path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $data['name'];
		$image->resize(new Box($width, $height))->save($resized_image_path);

		// Set the meta data to be stored on the document in GridFS
		$meta = array(
			'name' => $data['name']
		);

		// Write the image to GridFS
		$handle = fopen($resized_image_path, 'rb');
		
		$file = static::write($handle, $meta);
		fclose($handle);

		// Tidy up
		unlink($resized_image_path);
		
		// Return what should be the file object that write() returns... this will have an id to associate
		return $file;
	}

}
?>