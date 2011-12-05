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
<<<<<<< feature/upgrade-pre
use lithium\core\Environment;
=======
>>>>>>> HEAD~162
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
	$url = $params['request']->url;

	if (strpos($url, 'files/dav') === 0) { /* Do form auth only for non-dav requests. */
		$granted = Auth::check('token', $params['request'], array(
			'writeSession' => false, 'checkSession' => false
		));

		if (!$granted) {
			return new Response(array('status' => 401, 'body' => 'Access denied; invalid token.'));
		}
	} else {
		/* Redirect visitors to root to login first. */
		$skip = array('login', 'logout'));

		$granted = in_array($url, $skip);
		$granted = Auth::check('userLogin', $params['request']);
		$granted = $granted || (strpos($url, 'test') === 0 && !Environment::is('production'));

		if (!$granted) { /* Redirect visitors to root to login first. */
			return new Response(array('location' => 'Users::login'));
		}
	}
	return $chain->next($self, $params, $chain);
});

?>