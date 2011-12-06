<?php
/**
 * Configure Authentication and Access Control. Request are checked
 * against the session based form adapter.
 */
use lithium\security\Auth;
use lithium\action\Dispatcher;
use lithium\action\Response;
use lithium\core\Environment;

Auth::config(array(
	'userLogin' => array(
		'model' => 'User',
		'adapter' => 'Form',
		'fields' => array('email', 'password')
	)
));

Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	$url = $params['request']->url;
	$skip = array('login', 'logout', 'register');

	$granted = in_array($url, $skip);
	$granted = $granted || (strpos($url, 'test') === 0 && !Environment::is('production'));
	$granted = $granted || Auth::check('userLogin', $params['request']);

	if (!$granted) {
		/* Redirect all non-authenticated users to the register page. */
		return new Response(array('location' => 'Users::register'));
	}
	return $chain->next($self, $params, $chain);
});

?>