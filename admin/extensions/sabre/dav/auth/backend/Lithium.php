<?php


namespace admin\extensions\sabre\dav\auth\backend;

use lithium\security\Auth;

class Lithium extends \Sabre_DAV_Auth_Backend_AbstractBasic {

    protected function validateUserPass($username, $password) {
		return Auth::check('default', array(
			'email' => $username
		) + compact('password'));
	}
}

?>