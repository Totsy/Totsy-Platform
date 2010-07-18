<?php

use li3_payments\extensions\Payments;

Payments::config(array(
	'default' => array(
		'adapter' => 'AuthorizeNet',
		'login' => '3mVJKk363dcz',
		'key' => '3zr65H5abZ68H4Ye',
		'debug' => true,
		// 'login' => 'totsytest1138',
		'password' => 'MyBa8yV1P',
		'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
	)
));

?>