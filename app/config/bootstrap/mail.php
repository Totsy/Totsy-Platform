<?php

use lithium\core\Environment;

Environment::set('development', array(
	'mail' => array(
		'api_key' => '568106ff64d98574392dba282bc3267f',
		'secret' => '288e514c962cf8adcd82ff01938b861f',
		'api_url' => 'http://api.sailthru.com'
	)
));
Environment::set('production', array(
	'mail' => array(
		'api_key' => '568106ff64d98574392dba282bc3267f',
		'secret' => '288e514c962cf8adcd82ff01938b861f',
		'api_url' => 'http://api.sailthru.com'
	)
));

require_once LITHIUM_APP_PATH.'/libraries/sailthru/Sailthru.php';
Sailthru::__init(Environment::get('development'));

?>