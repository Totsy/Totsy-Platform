<?php

namespace admin\models;

use \lithium\data\Connections;
use \Imagine\Gd\Imagine;

class EventImage extends File {

	protected $_meta = array("source" => "fs.files");

	public $image_sizes = array(
		'event_logo' => array(100, 100),
		'event_image' => array(100, 100),
		'event_splash_big_image' => array(100, 100),
		'event_splash_small_image' => array(100, 100)
	);
	
	public function write($handle, $meta = array()) {
		
		echo 'console.dir('.json_encode($data).');';
		
		$imagine = new Imagine();
		$image = $imagine->open($data->tmp_name);
	//	$image->resize() 
		//$resized_tempfile = "{$tempfile}_{$width}x{$height}.png";
		//$image->resize($width, $height)->save($resized_tempfile);
		
		exit();
		//return parent::write($handle, $meta);
	}
	
}
?>