<?php

use li3_payments\extensions\Payments;

$dev = array(
	'adapter' => 'AuthorizeNet',
	'login' => '8bg66qYX2',
	'key' => '589CsNvKF4M5sf35',
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
