<?php

use lithium\core\Environment;

$base = array(
	'companyCode' => 'totsy',
	'trace' => true,
	'useAvatax' => true,
	'retriesNumber' => 2,
	'logEmail' => 'tax-notifications@totsy.com'
);

Environment::set('production', array(
	'avatax' => $base + array(
	    'url' => 'https://avatax.avalara.net',
	    'account' => '1100064978',
	    'license' => 'E96C0C6042CDD179'
	)
));
Environment::set('development', array(
	'avatax' => $base + array(
		'url' => 'https://avatax.avalara.net',
		'account' => '1100064978',
		'license' => 'E96C0C6042CDD179'
	)
));
Environment::set('test', array(
	'avatax' => $base + array(
		'url' => 'https://development.avalara.net',
		'account' => '1100058465',
		'license' => 'C4930DB03091446E'
	)
));
Environment::set('local', array(
	'avatax' => $base + array(
	    'url' => 'https://avatax.avalara.net',
	    'account' => '1100064978',
	    'license' => 'E96C0C6042CDD179'
	)
));

// must have Environment
require_once LITHIUM_APP_PATH . '/libraries/AvaTax4PHP/AvaTaxWrap.php';
AvaTaxWrap::__init('development', Environment::get('development'));

?>