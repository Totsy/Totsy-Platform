<?php
/**
 * Configure Authentication and Access Control. Request are first checked
 * against the HTTP auth adapter than the session based form adapter will be
 * used to authenticate the request.
 *
 * For login via form the default Lithium password validator is replaced by our
 * own logic using sha1 as the hashing algo. This allows us to reuse existing
 * user credentials. The validator function contains code copied from
 * Password::check(), preventig time-based attacks.
 */
use lithium\security\Auth;
use lithium\action\Dispatcher;
use lithium\action\Response;

Auth::config(array(
	'userLogin' => array(
		'model' => 'User',
		'adapter' => 'Form',
		'fields' => array('email', 'password'),
		'scope' => array('admin' => true),
		'validators' => array(
			'password' => function($password, $hash) {
				$password = sha1($password);
				$result = true;

				if (($length = strlen($password)) != strlen($hash)) {
					return false;
				}
				for ($i = 0; $i < $length; $i++) {
					$result = $result && ($password[$i] === $hash[$i]);
				}
				return $result;
			}
		)
	),
	'http' => array(
		'adapter' => 'Http',
		'method' => 'digest',
		'users' => array(
			'admin' => 'lbNUx5Ff!ND'
		)
	)
));

Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	$skip = array('login', 'logout');
	$url = $params['request']->url;

	if (in_array($url, $skip)) {
		return $chain->next($self, $params, $chain);
	}
	$granted = Auth::check('userLogin', $params['request']);

	if ($url == '/' && !$granted) { /* Redirect visitors to root to login first. */
		return new Response(array('location' => 'Users::login'));
	}
	$granted = $granted || Auth::check('http', $params['request'], array(
		'writeSession' => false, 'checkSession' => false
	));

	if (!$granted) {
		return new Response(array('status' => 401, 'body' => 'Access denied.'));
	}
	return $chain->next($self, $params, $chain);
});

?>