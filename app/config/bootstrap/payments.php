<?php

use li3_payments\extensions\Payments;

Payments::config(array(
	'default' => array(
		'adapter' => 'AuthorizeNet',
		'login' => '3Zuk5g64mFR',
		'key' => '3DgFTdNk4q9342yr',
		'debug' => true,
		// 'login' => 'totsytest1138',
		'password' => 'MyBa8yV1P',
		'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
	)
));

?>