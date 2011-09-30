<?php

use li3_payments\payments\Processor;

/**
 * Payment configuration.
 *
 * To generate a transaction key login to your account i.e. through
 * `https://ebctest.cybersource.com` and go to `Account Managmenet` ->
 * `Transaction Security Keys` -> `Security Keys for the SOAP Toolkit`.
 */

$key  = '<transaction key>';
$test = array(
	'adapter' => 'CyberSource',
	'merchantID' => '<merchant id>',
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