<?php

use li3_payments\extensions\Payments;

$dev = array(
	'adapter' => 'AuthorizeNet',
	'login' => '2SnmY78Yk',
	'key' => '3g9C74dy46v2jDF7',
	'debug' => false,
	'gateway' => 'test',
	'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
);

Payments::config(array(
	'default' => array(
		'production' => array(
			'adapter' => 'AuthorizeNet',
			'login' => '3Zuk5g64mFR',
			'key' => '3DgFTdNk4q9342yr',
			'debug' => false,
			'gateway' => 'live',
			'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
		),
		'test' => $dev,
		'development' => $dev,
		'local' => $dev
	)
));

?>