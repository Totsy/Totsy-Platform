<?php

namespace admin\models;

use \lithium\data\Connections;
use \Imagine\Gd\Imagine;
use \Imagine\Image\Box;

class EventImage extends File {

	protected $_meta = array("source" => "fs.files");

	/**
	 * Image sizes for event images.
	 * Specified in width, height
	 * 
	 * @var type array
	 */
	static $image_sizes = array(
		'logo' => array(148, 52),
		'image' => array(300, 193),
		'splash_big_image' => array(355, 410),
		'splash_small_image' => array(100, 100)
	);
	
	public static function resizeAndSave($position=null, $data=null) {
		if(empty($data) || !in_array($position, array_keys(EventImage::$image_sizes))) {
			return false;
		}
		
		// Resize the image
		$tmp_file = (isset($data['tmp_name'])) ? $data['tmp_name']:null;
		$imagine = new Imagine();
		$image = $imagine->open($tmp_file);
		$resized_image = $image->resize(new Box(EventImage::$image_sizes[$position][0], EventImage::$image_sizes[$position][1]))->save(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $data['name']);
		
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