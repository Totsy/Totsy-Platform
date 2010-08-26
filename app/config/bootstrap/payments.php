<?php

use li3_payments\extensions\Payments;

$dev = array(
	'adapter' => 'AuthorizeNet',
	'login' => '3mVJKk363dcz',
	'key' => '3zr65H5abZ68H4Ye',
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
	),
	'test' => $dev
));

?>