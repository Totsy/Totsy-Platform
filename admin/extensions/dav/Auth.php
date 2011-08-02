<?php


namespace admin\extensions\dav;

use lithium\security\Auth;

class Auth extends \Sabre_DAV_Auth_Backend_AbstractBasic {

    protected function validateUserPass($username, $password) {
		return Auth::check('default', array(
			'email' => $username
		) + compact('password'));
	}
}

?>