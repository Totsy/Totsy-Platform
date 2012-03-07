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
use admin\models\User;

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
	'token' => array(
		'adapter' => 'Token',
		'check' => function($token) {
			return User::first(array('conditions' => compact('token')));
		}
	)
));

Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	$allowed = false;
	#skip auth checker for image uploads
	if(preg_match('#(uploads/upload)#', $params['request']->url)) {
		$allowed = true;
	}
	$url = $params['request']->url;
	if (strpos($url, 'files/dav') === 0) { /* Do form auth only for non-dav requests. */
		$granted = $allowed || Auth::check('token', $params['request'], array(
		'writeSession' => false, 'checkSession' => false
		));
		if (!$granted) {
			return new Response(array('status' => 401, 'body' => 'Access denied; invalid token.'));
		}
	} else {

		if (in_array($url, array('login', 'logout'))) {
		    return $chain->next($self, $params, $chain);
	}

	$granted = in_array($params['request']->url, $skip);
	$granted = $allowed || Auth::check('userLogin', $params['request']);

	if (!$granted) { /* Redirect visitors to root to login first. */
	return new Response(array('location' => 'Users::login'));
	}

}

return $chain->next($self, $params, $chain);
});

?>