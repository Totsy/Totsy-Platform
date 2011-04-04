<?php

use li3_payments\extensions\Payments;

$dev = array(
	'adapter' => 'AuthorizeNet',
	'login' => '7uXvS44q',
	'key' => '5z4X93s7cq4P2tEQ',
	'debug' => false,
	'gateway' => 'test',
	'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
);

Payments::config(array(
	'default' => array(
		'production' => array(
			'adapter' => 'AuthorizeNet',
			'login' => '8M2rfU63AKzX',
			'key' => '8rLu652Ff932KUXD',
			'debug' => false,
			'gateway' => 'live',
			'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
		),
		'test' => $dev,
		'development' => $dev,
		'local' => $dev
	),
	'local' => $dev
));

?>
