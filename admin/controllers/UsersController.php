<?php

namespace admin\controllers;
use admin\models\User;
use \lithium\security\Auth;
use \lithium\storage\Session;
use \lithium\data\Connections;


/**
 * This class provides all the methods to register and authentic a user. 
 */

/*
	TODO The authenticaion process needs another look. We should be storing
	the users information in the session instead of the cookie. 
*/
class UsersController extends \lithium\action\Controller {

	public function index(){

	}
	/**
	 * Performs login authentication for a user going directly to the database.
	 * If authenticated the user will be redirected to the home page.
	 *
	 * @return string The user is prompted with a message if authentication failed.
	 */
	public function login() {
		$message = false;
		Auth::config(array('userLogin' => array(
			'model' => 'User',
			'adapter' => 'Form',
			'fields' => array('username', 'password'))
			));
		if ($this->request->data) {
			$auth = Auth::check("userLogin", $this->request);
			var_dump(Session::read('userLogin'));
			if ($auth == false) {
				$message = 'Login Failed - Please Try Again';
			} else {
				$this->redirect('/');
				
			}
		}

		//new login layout to account for fullscreen image JL
		$this->_render['layout'] = 'login';
		return compact('message');
	}
	/**
	 * Performs the logout action of the user removing '_id' from session details.
	 */
	public function logout() {
		Auth::config(array('userLogin' => array(
			'model' => 'User',
			'adapter' => 'Form',
			'fields' => array('username', 'password'))
			));
		Auth::clear('userLogin');
		$this->redirect(array('action'=>'login'));
	}

	/**
	 * @param array $sessionInfo
	 * @return boolean
	 */
	private function writeSession($sessionInfo) {
		return (Session::write('userLogin', $sessionInfo));
	}
	
	/**
	 * 
	 */


	
	public function updatePassword()
	{
		//If there is a request
			//New Passwords need to match
	
			//Get the user based on their session
	
			//If the user is legacy
				//If password is correct via authIllogic
					//Remove Salt
					//Change password using sha1
					//Set Legacy flag to 0
				//else
					//Message the user that the old password is incorrect
			//else 
				//Change the password using sha1
			//Send user message that their password has been updated
		
	}
}

?>