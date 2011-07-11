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
		'logEmail' => 'skosh@totsy.com',
		'useAvatax' => true
	)
));

Environment::set('development', array(
	'avatax' => array(
	    //'url' => 'https://development.avalara.net',
	    //'account' => '1100058465',
	    //'license' => 'C4930DB03091446E',
	   	'url' => 'https://avatax.avalara.net',
	    'account' => '1100064978',
	    'license' => 'E96C0C6042CDD179',
		'companyCode' => 'totsy',
	    'trace' => true,
		'retriesNumber' => 2,
		'logEmail' => 'skosh@totsy.com',
		'useAvatax' => true
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
		'logEmail' => 'skosh@totsy.com',
 		'useAvatax' => true
	)
));

Environment::set('production', array(
	'avatax' => array(
	    'url' => 'https://avatax.avalara.net',
	    'account' => '1100064978',
	    'license' => 'E96C0C6042CDD179',
		'companyCode' => 'totsy',
	    'trace' => false,
		'retriesNumber' => 1,
		'logEmail' => 'skosh@totsy.com',
		'useAvatax' => true
	)
));

// must have Environment
require_once LITHIUM_APP_PATH.'/libraries/AvaTax4PHP/AvaTaxWrap.php';
AvaTaxWrap::__init('development',Environment::get('development'));
?>