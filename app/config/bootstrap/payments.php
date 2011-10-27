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
		if (!Environment::is('production') && $name == 'authorizenet') {
			return false;
		}
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

$key = 'BexYoSnNAjU/1+osPIPukh0uYy4qf8tc7+f2Xb107q4Y1tI6tCHSdzdtDxgyAKzpb9IrD6vwxca6OMadqpcC2WuFUN2gWIsXnyEpAkEAjpNShVS1Ex0GkEi5/+7C0pMKKVgL5celaTLwYLH/Bnb8dXwNp+/aOogskyIApmZ2j0JbXJuLr5+r/ZEuTWKrChIDHS5jLip/y1zv5/ZdvXTurhjW0jq0IdJ3N20PGDIArOlv0isPq/DFxro4xp2qlwLZa4VQ3aBYixefISkCQQCOk1KFVLUTHQaQSLn/7sLSkwopWAvlx6VpMvBgsf8Gdvx1fA2n79o6iCyTIgCmZnaPQg==';
$live = array(
	'adapter' => 'CyberSource',
	'merchantID' => 'totsy',
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
	'authorizenet' => array(
		'adapter' => 'AuthorizeNet',
		'login' => '8M2rfU63AKzX',
		'key' => '2J6978WzN6WV6jb7',
		'debug' => false,
		'endpoint' => 'live',
		'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl')),
		'filters' => array(
			'alwaysProcessAdapter' => true,
			'adapter' => $adapterFilters['authorizenet']
		)
	),
	'authorizenet-test' => array(
		'adapter' => 'AuthorizeNet',
		'login' => '7uXvS44q',
		'key' => '5z4X93s7cq4P2tEQ',
		'debug' => false,
		'endpoint' => 'test',
		'connection' => array('classes' => array('socket' => 'lithium\net\socket\Curl')),
		'filters' => array(
			'alwaysProcessAdapter' => true,
			'adapter' => $adapterFilters['authorizenet']
		)
	),
	'local' => $test,
	'test' => $test
));

?>