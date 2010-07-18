<?php

use li3_payments\extensions\Payments;

Payments::config(array(
	'default' => array(
		'adapter' => 'AuthorizeNet',
		'login' => '3Zuk5g64mFR',
		'key' => '3DgFTdNk4q9342yr',
		'debug' => true,
		'gateway' => 'live',
		'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
	)
));

?>