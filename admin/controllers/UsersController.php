<?php

namespace admin\controllers;
use admin\models\User;
use \lithium\security\Auth;
use \lithium\storage\Session;
use \lithium\data\Connections;
use \lithium\analysis\Logger;

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
		Auth::config(array('userLogin' => array(
			'model' => 'User',
			'adapter' => 'Form',
			'fields' => array('username', 'password'))
			));
		if ($this->request->data) {
			$username = $this->request->data['username'];
			$password = $this->request->data['password'];
			//Grab User Record
			$this->userRecord = User::find('first', array(
				'conditions' => array('username' => "$username")
			));
			if(!empty($this->userRecord)){
				if($this->userRecord->legacy == 1) {
					$successAuth = $this->authIllogic($password);
					if ($successAuth) {
						//Write core information to the session and redirect user
						$sessionWrite = $this->writeSession($this->userRecord->data());
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
	private function authIllogic($password) {
		$digest = $password . $this->userRecord->data('salt');
	    for ($i = 0; $i < 20; $i++) {
			$digest = hash('sha512', $digest);
	    }
		return $digest == $this->userRecord->data('password');
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
	public function loginzuno(){
		
		$this->_render['layout'] = 'loginzuno';
	
	}
	
	protected function _init() {
		parent::_init();
		
		$MongoDb = Connections::get('default');
		$MongoDb->applyFilter('read', function($self, $params, $chain) use (&$MongoDb) {
			$result = $chain->next($self, $params, $chain);
			if (method_exists($result, 'data')) {
				Logger::write('info',
					json_encode($params['query']->export($MongoDb) + array('result' => $result->data()))
				);
			}
			return $result;
		});
	}
	
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