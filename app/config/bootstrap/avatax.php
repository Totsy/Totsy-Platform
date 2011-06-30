<?php

use lithium\core\Environment;

Environment::set('test', array(
	'avatax' => array(
	    'url' => 'https://development.avalara.net',
	    'account' => '1100058465',
	    'license' => 'C4930DB03091446E',
		'companyCode' => 'totsy',
	    'trace' => true
	)
));

Environment::set('development', array(
	'avatax' => array(
	    'url' => 'https://development.avalara.net',
	    'account' => '1100058465',
	    'license' => 'C4930DB03091446E',
		'companyCode' => 'totsy',
	    'trace' => true
	)
));

Environment::set('local', array(
	'avatax' => array(
	    'url' => 'https://development.avalara.net',
	    'account' => '1100058465',
	    'license' => 'C4930DB03091446E',
		'companyCode' => 'totsy',
	    'trace' => true
	)
));

Environment::set('production', array(
	'avatax' => array(
	    'url' => 'https://avatax.avalara.net',
	    'account' => '< prod account number >',
	    'license' => '< prod license key >',
		'companyCode' => '< prod company code >',
	    'trace' => false
	)
));

// must have Environment
require_once LITHIUM_APP_PATH.'/libraries/AvaTax4PHP/AvaTaxWrap.php';
AvaTaxWrap::__init('development',Environment::get('development'));
?>