<?php

namespace admin\models;

use \lithium\data\Connections;
use \Imagine\Gd\Imagine;

class EventImage extends File {

	protected $_meta = array("source" => "fs.files");
	
	public function save($entity, $data = null, array $options = array()) {
		
		$imagine = new Imagine();
		//$image = $imagine->open($tempfile);
		//$resized_tempfile = "{$tempfile}_{$width}x{$height}.png";
		//$image->resize($width, $height)->save($resized_tempfile);
		
		return parent::save($entity, $data, $options);
	}
	
}
?>