<?php

use lithium\core\Environment;

Environment::set('test', array(
	'mail' => array(
		'host'  => 'relay.jangosmtp.net',
		'port'  => 25,
		'username'  => 'mitchy',
		'password'  => '413118BI',
		'domain' => 'test.totsy.com'
	)
));
Environment::set('staging', array(
	'mail' => array(
	
	)
));
Environment::set('development', array(
	'mail' => array(
	
	)
));
Environment::set('production', array(
	'mail' => array(
		'host'  => 'relay.jangosmtp.net',
		'port'  => 25,
		'username'  => 'mitchy',
		'password'  => '413118BI',
		'domain' => 'totsy.com'
	)
));

?>