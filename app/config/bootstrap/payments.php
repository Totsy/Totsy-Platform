<?php

use li3_payments\payments\Processor;
use lithium\core\Environment;

$adapterFilters = array(
	'cybersource' => function($function, $params, $name) {
		$options = $params['options'];

		switch ($function) {
			case 'authorize' && $params['pmt']->type == 'amex':
				return true;
				break;
		}
		if (isset($options['processor']) && $options['processor'] == 'CyberSource') {
			return true;
		}
	},
	'authorizenet' => function($function, $params, $name) {
		$options = $params['options'];

		$processor = isset($options['processor']) ? $options['processor'] : false;
		if (!$processor || $processor == 'AuthorizeNet') {
			return true;
		}
	}
);

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
	'endpoint' => 'test',
	'filters' => array(
		'alwaysProcessAdapter' => true,
		'adapter' => $adapterFilters['cybersource']
	)
);

$key  = '<transaction key>';
$live = array(
	'adapter' => 'CyberSource',
	'merchantID' => '<merchant id>',
	'transactionKey' => $key,
	'endpoint' => 'live',
	'filters' => array(
		'alwaysProcessAdapter' => true,
		'adapter' => $adapterFilters['cybersource']
	)
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