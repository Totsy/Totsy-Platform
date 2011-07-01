<?php

use lithium\core\Environment;

Environment::set('test', array(
	'avatax' => array(
	    'url' => 'https://development.avalara.net',
	    'account' => '1100058465',
	    'license' => 'C4930DB03091446E',
		'companyCode' => 'totsy',
	    'trace' => true,
		'retriesNumber' => 2,
		'logEmail' = 'skosh@totsy.com'
	)
));

Environment::set('development', array(
	'avatax' => array(
	    'url' => 'https://development.avalara.net',
	    'account' => '1100058465',
	    'license' => 'C4930DB03091446E',
		'companyCode' => 'totsy',
	    'trace' => true,
		'retriesNumber' => 2,
		'logEmail' = 'skosh@totsy.com'
	)
));

Environment::set('local', array(
	'avatax' => array(
	    'url' => 'https://development.avalara.net',
	    'account' => '1100058465',
	    'license' => 'C4930DB03091446E',
		'companyCode' => 'totsy',
	    'trace' => true,
		'retriesNumber' => 2,
		'logEmail' = 'skosh@totsy.com'
	)
));

Environment::set('production', array(
	'avatax' => array(
	    'url' => 'https://avatax.avalara.net',
	    'account' => '< prod account number >',
	    'license' => '< prod license key >',
		'companyCode' => '< prod company code >',
	    'trace' => false,
		'retriesNumber' => 1,
		'logEmail' = 'skosh@totsy.com'
	)
));

// must have Environment
require_once LITHIUM_APP_PATH.'/libraries/AvaTax4PHP/AvaTaxWrap.php';
AvaTaxWrap::__init('development',Environment::get('development'));
?>