<?php

use li3_payments\payments\Processor;
use lithium\core\Environment;

$adapterFilters = array(
	'cybersource' => function($function, $params, $name) {
		$options = $params['options'];

		switch ($function) {
			case 'authorize':
				return true;
				break;
			case 'credit':
				return true;
				break;
			case 'profile':
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
		if (!$processor) {
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
$cybersourceTest = array(
	'adapter' => 'CyberSource',
	'merchantID' => 'totsy',
	'transactionKey' => 'BexYoSnNAjU/1+osPIPukh0uYy4qf8tc7+f2Xb107q4Y1tI6tCHSdzdtDxgyAKzpb9IrD6vwxca6OMadqpcC2WuFUN2gWIsXnyEpAkEAjpNShVS1Ex0GkEi5/+7C0pMKKVgL5celaTLwYLH/Bnb8dXwNp+/aOogskyIApmZ2j0JbXJuLr5+r/ZEuTWKrChIDHS5jLip/y1zv5/ZdvXTurhjW0jq0IdJ3N20PGDIArOlv0isPq/DFxro4xp2qlwLZa4VQ3aBYixefISkCQQCOk1KFVLUTHQaQSLn/7sLSkwopWAvlx6VpMvBgsf8Gdvx1fA2n79o6iCyTIgCmZnaPQg==',
	'endpoint' => 'test',
	'filters' => array(
		'alwaysProcessAdapter' => true,
		'adapter' => $adapterFilters['cybersource']
	)
);

$cybersourceProduction = array(
	'adapter' => 'CyberSource',
	'merchantID' => 'totsy',
	'transactionKey' => 'BexYoSnNAjU/1+osPIPukh0uYy4qf8tc7+f2Xb107q4Y1tI6tCHSdzdtDxgyAKzpb9IrD6vwxca6OMadqpcC2WuFUN2gWIsXnyEpAkEAjpNShVS1Ex0GkEi5/+7C0pMKKVgL5celaTLwYLH/Bnb8dXwNp+/aOogskyIApmZ2j0JbXJuLr5+r/ZEuTWKrChIDHS5jLip/y1zv5/ZdvXTurhjW0jq0IdJ3N20PGDIArOlv0isPq/DFxro4xp2qlwLZa4VQ3aBYixefISkCQQCOk1KFVLUTHQaQSLn/7sLSkwopWAvlx6VpMvBgsf8Gdvx1fA2n79o6iCyTIgCmZnaPQg==',
	'endpoint' => 'live',
	'filters' => array(
		'alwaysProcessAdapter' => true,
		'adapter' => $adapterFilters['cybersource']
	)
);

$authorizenetTest = array(
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
);

$authorizenetProduction = array(
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
);

Processor::config(array(
	'default' => array(
		'production' => $cybersourceProduction,
		'test' => $cybersourceTest,
		'staging' => $cybersourceProduction,
		'development' => $cybersourceTest,
		'local' => $cybersourceTest
	),
	'authorizenet' => array(
		'production' => $authorizenetProduction,
		'test' => $authorizenetTest,
		'staging' => $authorizenetProduction,
		'development' => $authorizenetTest,
		'local' => $authorizenetTest
	),
	'local' => $cybersourceTest,
	'test' => $cybersourceTest,
	'staging' => $cybersourceProduction
));

?>
