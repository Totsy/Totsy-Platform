<?php

namespace admin\models;

class ItemImage extends \admin\models\Image {

	public static $types = array(
		'primary' => array(
			'dimensions' => array(298, 300),
			'field' => 'primary_image',
			'multiple' => false,
			'regex' => array(
				'/^items\_.+\_p(rimary)?\..*/i'
			),
			'uploadName' => array(
				'form' => 'items_{:url}_{:name}.jpg',
				'dav' => '/events/{:year}/{:month}/{:event}/_items/{:item}/{:name}/{:file}.jpg'
			)
		),
		'zoom' => array(
			'dimensions' => array(596, 600),
			'field' => 'zoom_image',
			'multiple' => false,
			'regex' => array(
				'/^items\_.+\_z(oom)?\..*/i'
			),
			'uploadName' => array(
				'form' => 'items_{:url}_{:name}.jpg',
				'dav' => '/events/{:year}/{:month}/{:event}/_items/{:item}/{:name}/{:file}.jpg'
			)
		),
		'alternate' => array(
			'dimensions' => array(298, 300),
			'field' => 'alternate_images',
			'multiple' => true,
			'regex' => array(
				'/^items\_.+\_a(lternate)?.+\..*/i',
				'/^items\_.+\_a(lternate)?\..*/i'
			),
			'uploadName' => array(
				'form' => 'items_{:url}_{:name}.jpg',
				'dav' => '/events/{:year}/{:month}/{:event}/_items/{:item}/{:name}/{:file}.jpg'
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