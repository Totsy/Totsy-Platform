<?php

use li3_payments\payments\Processor;

$dev = array(
	'adapter' => 'AuthorizeNet',
	'login' => '7uXvS44q',
	'key' => '5z4X93s7cq4P2tEQ',
	'debug' => false,
	'endpoint' => 'test',
	'connection' => array(
		'classes' => array('socket' => 'lithium\net\socket\Curl'),
		'socket' => 'lithium\net\socket\Curl'
	)
);

Processor::config(array(
	'default' => array(
		'production' => array(
			'adapter' => 'AuthorizeNet',
			'login' => '8M2rfU63AKzX',
			'key' => '2J6978WzN6WV6jb7',
			'debug' => false,
			'endpoint' => 'live',
			'connection' => array(
				'classes' => array('socket' => 'lithium\net\socket\Curl'),
				'socket' => 'lithium\net\socket\Curl'
			)
		),
		'test' => $dev,
		'development' => $dev,
		'local' => $dev
	),
	'local' => $dev,
	'test' => $dev
));

?>