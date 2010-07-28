<?php

use li3_payments\extensions\Payments;

Payments::config(array(
	'default' => array(
		'adapter' => 'AuthorizeNet',
		'login' => '3mVJKk363dcz',
		'key' => '3zr65H5abZ68H4Ye',
		'debug' => false,
		'gateway' => 'test',
		'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl'))
	)
));

?>