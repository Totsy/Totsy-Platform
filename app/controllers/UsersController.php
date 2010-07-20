<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\User;
use app\models\Menu;
use \lithium\security\Auth;
use \lithium\storage\Session;
use app\extensions\Mailer;


/**
 * This class provides all the methods to register and authentic a user. 
 */

/*
	TODO The authenticaion process needs another look. We should be storing
	the users information in the session instead of the cookie. 
*/
class UsersController extends BaseController {

	protected function _init() {
		parent::_init();
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$userInfo = Session::read('userLogin');
			$self->set(compact('userInfo'));
			return $chain->next($self, $params, $chain);
		});
	}
	public function index(){

	}
	/**
	 * Performs basic registration functionality. All validation checks should happen via
	 * JavaScript so no empty data is going into Mongo.
	 * @todo Refactor to use count() from Mongo instead of array PHP count
	 * @todo Confirm redirect location and message upon successful registration
	 * @todo Authenticate upon successful registration before redirect
	 * @return string User will be promoted that email is already registered.
	 */
	public function register() {
		$message = false;
		if ($this->request->data) {
			$this->request->data['password'] = sha1($this->request->data['password']);
			$email = $this->request->data['email'];
			$username = $this->request->data['username'];
			//Check if email exists
			$emailCheck = User::count(array('email' => "$email"));
			//Check if username exists
			$usernameCheck = User::count(array('username' => "$username"));
			if (empty($emailCheck) && empty($usernameCheck)) {
				$user = User::create();
				$success = $user->save($this->request->data);
				if ($success) {
					$id = Session::write('_id', $user->_id);
					$firstname = Session::write('firstname', $user->firstname);
					$lastname = Session::write('lastname', $user->lastname);
					$email = Session::write('email', $user->email);
					Mailer::send(
						'welcome',
						'Welcome to Totsy!',
						array('name' => $user->firstname, 'email' => $user->email),
						compact('user')
					);
					$this->redirect('/account/details');
				}
			} else {
				$message = 'This email/username is already registered';
			}
		}
		$this->_render['layout'] = 'login';
		return compact('message');
	}
	/**
	 * Performs login authentication for a user going directly to the database.
	 * If authenticated the user will be redirected to the home page.
	 *
	 * @return string The user is prompted with a message if authentication failed.
	 */
	public function login() {
		$message = false;
		if ($this->request->data) {
			$username = $this->request->data['username'];
			$password = $this->request->data['password'];
			//Grab User Record
			$user = User::first(array('conditions' => compact('username')));

			if($user){
				if($user->legacy == 1) {
					$successAuth = $this->authIllogic($password, $user);
					if ($successAuth) {
						//Write core information to the session and redirect user
						$sessionWrite = $this->writeSession($user->data());
						$this->redirect('/');
					} else {
						$message = 'Login Failed - Please Try Again';
					}
				} else {
					$auth = Auth::check("userLogin", $this->request);
					if ($auth == false) {
						$message = 'Login Failed - Please Try Again';
					} else {
						$this->redirect('/');
					}
				}
			}
			/*
				TODO Update the lastlogin time, ip address, and login counter
			*/
		}
		//new login layout to account for fullscreen image JL
		$this->_render['layout'] = 'login';
		return compact('message');
	}
	/**
	 * Performs the logout action of the user removing '_id' from session details.
	 */
	public function logout() {
		$success = Session::delete('userLogin');
		$this->redirect(array('action'=>'login'));
	}
	/**
	 * This is only for legacy users that are coming with AuthLogic passwords and salt
	 * @param string $password
	 * @return boolean
	 */
	private function authIllogic($password, $user) {
		$digest = $password . $user->salt;
	    for ($i = 0; $i < 20; $i++) {
			$digest = hash('sha512', $digest);
	    }
		return $digest == $user->password;
	}
	/**
	 * @param array $sessionInfo
	 * @return boolean
	 */
	private function writeSession($sessionInfo) {
		return (Session::write('userLogin', $sessionInfo));
	}
	
	/**
	 * Updates the user information including password.
	 * 
	 * @return array
	 */
	public function info() {
		$status = 'default';
		$user = User::getUser();
		if ($this->request->data) {
			$oldPass = $this->request->data['password'];
			$newPass = $this->request->data['new_password'];
			if ($user->legacy == '1') {
				$status = ($this->authIllogic($oldPass, $user)) ? 'true' : 'false';
			} else {
				$status = (sha1($oldPass) == $user->password) ? 'true' : 'false';
			}
			if ($status == 'true') {
				$user->password = sha1($newPass);
				$user->legacy == false;
				unset($this->request->data['password']);
				unset($this->request->data['new_password']);
				if ($user->save($this->request->data)) {
					$info = Session::read('userLogin');
					$info['firstname'] = $this->request->data['firstname'];
					$info['lastname'] = $this->request->data['lastname'];
					Session::write('userLogin', $info);
				}
			}
		}
		return compact("user", "status");

	}

	public function reset() {
		$this->render(array('layout' => false));
		if ($this->request->data) {
			$user = User::find('first', array(
				'conditions' => array(
					'email' => $this->request->data['email']
			)));
			if ($user) {
				$clearText = $this->generatePassword();
				$password = sha1($clearText);
				$lastip = $this->request->env('REMOTE_ADDR');
				if ($user = User::process($user, $password, $lastip)) {
					die(var_dump($user,$clearText));
					Mailer::send(
						'welcome',
						'Welcome to Totsy!',
						array('name' => $user->firstname, 'email' => $user->email),
						compact('user')
					);
					$message = "Your password has been reset. Please check your email";
				} else {
					$message = "Sorry your password has not been reset. Please try again.";
				}
			} else {
				$message = "This email doesn't exist.";
			}
		}
		return compact("message");
	}

	protected function generatePassword() {
        return substr(md5(uniqid(rand(),1)), 1, 10);
    }
}

?>