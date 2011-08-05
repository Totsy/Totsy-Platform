<?php

namespace admin\models;

class EventImage extends \admin\models\Image {

	public static $types = array(
		'event' => array(
			'dimensions' => array(300, 193),
			'field' => 'event_image',
			'multiple' => false,
			'regex' => array(
				/* Event Image (2 ways to name) */
				'/^events\_.+\_image\..*/i',
				/* Matches: events_pretty-url.jpg */
				/* ...but events_pretty-url_anything... won't be matched. */
				'/^events\_.+(?<!\_|\_logo|\_big\_splash|\_small\_splash|\_splash\_small|\_splash\_big)\..*/i'
			),
			'uploadName' => array(
				'form' => 'events_{:url}_image.jpg',
				'dav' => '/events/{:year}/{:month}/{:event}/event/{:file}.jpg'
			)
		),
		'logo' => array(
			'dimensions' => array(148, 52),
			'field' => 'logo_image',
			'multiple' => false,
			'regex' => array(
				'/^events\_.+\_logo\..*/i'
			),
			'uploadName' => array(
				'form' => 'events_{:url}_{:name}.jpg',
				'dav' => '/events/{:year}/{:month}/{:event}/{:name}/{:file}.jpg'
			)
		),
		'splash_big' => array(
			'dimensions' =>  array(355, 410),
			'field' => 'splash_big_image',
			'multiple' => false,
			'regex' => array(
				/* Event Big Splash Image (2 ways to name) */
				'/^events\_.+\_big\_splash\..*/i',
				'/^events\_.+\_splash\_big\..*/i'
			),
			'uploadName' => array(
				'form' => 'events_{:url}_{:name}.jpg',
				'dav' => '/events/{:year}/{:month}/{:event}/{:name}/{:file}.jpg'
			)
		),
		'splash_small' => array(
			'dimensions' => array(298, 344),
			'field' => 'splash_small_image',
			'multiple' => false,
			'regex' => array(
				/* Event Small Splash Image (2 ways to name) */
				'/^events\_.+\_small\_splash\..*/i',
				'/^events\_.+\_splash\_small\..*/i'
			),
			'uploadName' => array(
				'form' => 'events_{:url}_{:name}.jpg',
				'dav' => '/events/{:year}/{:month}/{:event}/{:name}/{:file}.jpg'
			)
		)
	);
}
?>