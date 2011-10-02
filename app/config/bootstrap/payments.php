<?php

use li3_payments\payments\Processor;

/**
 * Payment configuration.
 *
 * To generate a transaction key login to your account i.e. through
 * `https://ebctest.cybersource.com` and go to `Account Managmenet` ->
 * `Transaction Security Keys` -> `Security Keys for the SOAP Toolkit`.
 */
$key = 'uYBI7tVxPce91/BmZkOHjnSGF1gfTPHC0Ba+vYRyvcFUY3KyeLaQlVBjDU3XqWvraaJLZFy7kFRfPrd/Yz6UYEHQI5ZPFZUfzEUN9ltgGSU+NonwiZw/w1cXRRJNiOI2BgkPZbrZl+Z4YWuqiwXIF/Kfr7rvhd17WW4rwvUsOfgOEdeyvOjTIIv0izWZc3ZtdIYXWB9M8cLQFr69hHK9wVRjcrJ4tpCVUGMNTdepa+tpoktkXLuQVF8+t39jPpRgQdAjlk8VlR/MRQ32W2AZJT42ifCJnD/DVxdFEk2I4jYGCQ9lutmX5nhha6qLBcgX8p+vuu+F3XtZbivC9Sw5+A==';
$test = array(
	'adapter' => 'CyberSource',
	'merchantID' => 'hlince',
	'transactionKey' => $key,
	'endpoint' => 'test'
);

$key  = '<transaction key>';
$live = array(
	'adapter' => 'CyberSource',
	'merchantID' => '<merchant id>',
	'transactionKey' => $key,
	'endpoint' => 'live'
);

Processor::config(array(
	'default' => array(
		'production' => $live,
		'test' => $test,
		'development' => $test,
		'local' => $test
	),
	'local' => $test,
	'test' => $test
));

?>