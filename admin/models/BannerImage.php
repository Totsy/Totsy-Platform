<?php

namespace admin\models;

class BannerImage extends \admin\models\Image {

	public static $types = array(
		'dinkers' => array(
			'dimensions' => array(230, 403),
			'field' => 'img2',
			'multiple' => true,
			'regex' => array(
				'/^banners\_.+\_dinkers\..*/i'
			),
			'uploadName' => array(
				'form' => 'banners_{:url}_{:name}.jpg',
				'dav' => '/banners/{:year}/{:month}/{:event}/{:name}/{:file}.jpg'
			)
		)
		
	);
}

?>