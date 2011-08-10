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
		'splash_small' => array('dimensions' => array(100, 100)
	);

	public static function resizeAndSave($position=null, $data=null) {
		if(empty($data) || !isset(static::$types[$position])) {
			return false;
		}
		list($width, $height) = static::$types[$position];

		// Resize the image
		$tmp_file = (isset($data['tmp_name'])) ? $data['tmp_name']:null;
		$imagine = new Imagine();
		$image = $imagine->open($tmp_file);
		$resized_image = $image->resize(new Box($width, $height))->save(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $data['name']);

		// Set the meta data to be stored on the document in GridFS
		$meta = array(
			'name' => $data['name']
		);

		// Write the image to GridFS
		$handle = fopen($resized_image);
		self::write($handle, $meta);
		fclose($handle);

		// Tidy up
		unlink($resized_image);
	}

}
?>