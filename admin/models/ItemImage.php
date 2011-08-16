<?php

namespace admin\models;

class ItemImage extends \admin\models\Image {

	public static $types = array(
		'primary' => array(
			'dimensions' => array(298, 300),
			'field' => 'primary_image',
			'multiple' => false,
			'regex' => array(
				'/^items\_.+\_primary\..*/i'
			)
		),
		'zoom' => array(
			'dimensions' => array(596, 600),
			'field' => 'zoom_image',
			'multiple' => false,
			'regex' => array(
				'/^items\_.+\_zoom\..*/i'
			)
		),
		'alternate' => array(
			'dimensions' => array(298, 300),
			'field' => 'alternate_images',
			'multiple' => true,
			'regex' => array(
				'/^items\_.+\_alternate.+\..*/i',
				'/^items\_.+\_alternate\..*/i'
			)
		),
		/* Not stored in GridFS. */
//		'cart' => array(
//			'dimensions' => array(60, 60),
//			'field' => 'cart_image',
//			'multiple' => false,
//			'regex' => array(
//				'/^items\_.+\_cart\..*/i'
//			)
//		),
//		'event_thumbnail' => array(
//			'dimensions' => array(93, 93),
//			'field' => 'event_thumbnail_image',
//			'multiple' => false,
//			'regex' => array(
//				'/^items\_.+\_event_thumbnail\..*/i'
//			)
//		)
	);
}

?>