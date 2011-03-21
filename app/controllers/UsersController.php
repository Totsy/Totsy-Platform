<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\User;
use app\models\Menu;
use app\models\Invitation;
use lithium\security\Auth;
use lithium\storage\Session;
use app\extensions\Mailer;
use app\extensions\Keyade;
use li3_silverpop\extensions\Silverpop;
use MongoDate;

class UsersController extends BaseController {

	public $sessionKey = 'userLogin';

	/**
	 * Performs registration functionality.
	 *
	 * The registration process takes into account the invitation code that a customer came
	 * in with. For instance, if the url is www.totsy.com/join/our365 then that code is saved
	 * as the invited_by field in mongo.
	 *
	 * During the registration process the user is also given an invitation code that they can use
	 * to invite others to Totsy. They are sent a welcome email and redirected to either the event
	 * page or a landing page based on the invitation url.
	 * If a user came from track.totsy.com via Keyade, pull the $affiliate_user_id from the URL and
	 * add to the user document.
	 * @params string $invite_code, string $affiliate_user_id
	 * @return string User will be promoted that email is already registered.
	 */
	public function register($invite_code = null, $affiliate_user_id = null) {
		$message = false;
		$data = $this->request->data;
		/*
		* redirects to the affiliate registration page if the left the page
		* and then decided to register after words.
		*/
		if(Session::check('cookieCrumb', array('name' => 'cookie'))){
			$cookie = Session::read('cookieCrumb', array('name' => 'cookie'));
			if(preg_match('(/a/)', $cookie['landing_url'])){
				$this->redirect($cookie['landing_url']);
			}
		}
		if (isset($data) && $this->request->data) {
			$data['emailcheck'] = ($data['email'] == $data['confirmemail']) ? true : false;
			$data['email'] = strtolower($this->request->data['email']);
		}
		$user = User::create($data);
		if ($this->request->data && $user->validates() ) {
			$email = $data['email'];
			$data['password'] = sha1($this->request->data['password']);
			$data['created_date'] = new MongoDate();
			$data['invitation_codes'] = substr($email, 0, strpos($email, '@'));
			$data['invited_by'] = $invite_code;
			$inviteCheck = User::count(array('invitation_codes' => $data['invitation_codes']));
			if ($inviteCheck > 0) {
				$data['invitation_codes'] = array(static::randomString());
			}
			if ($invite_code) {
				$inviter = User::find('first', array(
					'conditions' => array(
						'invitation_codes' => array($invite_code)
				)));
				if ($inviter) {
					$invited = Invitation::find('first', array(
						'conditions' => array(
							'user_id' => (string) $inviter->_id,
							'email' => $email
					)))	;
					if ($invited) {
						$invited->status = 'Accepted';
						$invited->date_updated = Invitation::dates('now');
						$invited->save();
						if ($invite_code != 'keyade') {
							Invitation::reject($inviter->_id, $email);
						}
					} else {
						$invitation = Invitation::create();
						$invitation->user_id = $inviter->_id;
						$invitation->email = $email;
						$invitation->date_accepted = Invitation::dates('now');
						$invitation->status = 'Accepted';
						$invitation->save();
					}
				}
			}
			switch ($invite_code) {
				case 'our365':
				case 'our365widget':
					$this->_render['template'] = 'our365';
					break;
				case 'keyade':
					$this->_render['template'] = 'keyade';
					if($affiliate_user_id){
						$data['keyade_user_id'] = $affiliate_user_id;
					}
			}
			if ($user->save($data)) {
				$userLogin = array(
					'_id' => (string) $user->_id,
				//	'firstname' => $user->firstname,
				//	'lastname' => $user->lastname,
				//	'zip' => $user->zip,
					'email' => $user->email
				);
				Session::write('userLogin', $userLogin, array('name'=>'default'));
				$data = array(
					'user' => $user,
					'email' => $user->email
				);
				Silverpop::send('registration', $data);
				$ipaddress = $this->request->env('REMOTE_ADDR');
				User::log($ipaddress);
				$this->redirect('/sales');
			}
		}
		$this->_render['layout'] = 'login';
		return compact('message', 'user');
	}

	/**
	 * This static method is a temporary solution for controller based registration (non-user).
	 * We'll need to refactor the `register` method along with `registration` so that there is more
	 * code reuse.
	 *
	 * @param array $data
	 * @return boolean
	 */
		public static function registration($data = null) {
			$saved = false;
			if ($data) {
				$data['email'] = strtolower($data['email']);
				$data['emailcheck'] = ($data['email'] == $data['confirmemail']) ? true : false;
				$user = User::create($data);
				if ($user->validates()) {
					$email = $data['email'];
					$data['password'] = sha1($data['password']);
					$data['created_date'] = User::dates('now');
					$data['invitation_codes'] = substr($email, 0, strpos($email, '@'));
					$inviteCheck = User::count( array(
							'invitation_codes' => $data['invitation_codes']
							));
					if ($inviteCheck > 0) {
						$data['invitation_codes'] = array(static::randomString());
					}
					if ($saved = $user->save($data)) {
						$data = array(
							'user' => $user,
							'email' => $user->email
						);
						Silverpop::send('registration', $data);
					}
				}
			}
			return compact('saved','user');
			/**
			* @see app/controllers/MomOfTheWeeksController.php
			**/
		}
	/**
	 * Performs login authentication for a user going directly to the database.
	 * If authenticated the user will be redirected to the home page.
	 *
	 * @return string The user is prompted with a message if authentication failed.
	 */
	public function login() {
		$message = $resetAuth = $legacyAuth = $nativeAuth = false;
		$rememberHash = '';
		$this->autoLogin();
		if ($this->request->data) {
			$email = trim(strtolower($this->request->data['email']));
			$password = trim($this->request->data['password']);
			$this->request->data['password'] = trim($this->request->data['password']);
			$this->request->data['email'] = trim($this->request->data['email']);
			//Grab User Record
			$user = User::lookup($email);
			$redirect = '/sales';
			if (strlen($password) > 0) {
				if($user){
					if (!empty($user->reset_token)) {
						if (strlen($user->reset_token) > 1) {
							$resetAuth = (sha1($password) == $user->reset_token) ? true : false;
							$redirect = 'users/password';
						}
					}
					if ($user->legacy == 1) {
						$legacyAuth = $this->authIllogic($password, $user);
					} else {
						$nativeAuth = (sha1($password) == $user->password) ? true : false;
					}
					if ($resetAuth || $legacyAuth || $nativeAuth) {
						$sessionWrite = $this->writeSession($user->data());
						$ipaddress = $this->request->env('REMOTE_ADDR');
						User::log($ipaddress);
						$cookie = Session::read('cookieCrumb', array('name' => 'cookie'));
            			$userInfo = Session::read('userLogin');
            			$cookie['user_id'] = $user['_id'];
            			if(array_key_exists('redirect', $cookie) && $cookie['redirect'] ) {
							$redirect = substr(htmlspecialchars_decode($cookie['redirect']),strlen('http://'.$_SERVER['HTTP_HOST']));
							unset($cookie['redirect']);
						}
            			Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
						User::rememberMeWrite($this->request->data['remember_me']);
						if (preg_match( '@[^(/|login)]@', $this->request->url ) && $this->request->url) {
							$this->redirect($this->request->url);
						} else {
							$this->redirect($redirect);
						}
					} else {
						$message = '<div class="error_flash">Login Failed - Please Try Again</div>';
					}
				}
			} else {
				$message = '<div class="error_flash">Login Failed - Your Password Is Blank</div>';
			}
		}
		//new login layout to account for fullscreen image JL
		$this->_render['layout'] = 'login';
		return compact('message');
	}
	protected function autoLogin(){
		$redirect = '/sales';
		$cookie = Session::read('cookieCrumb', array('name' => 'cookie'));
		if(preg_match( '@[(/|login)]@', $this->request->url ) && $cookie && array_key_exists('autoLoginHash', $cookie)) {
			$user = User::find('first', array('conditions' => array('autologinHash' => $cookie['autoLoginHash'])));
			if($user) {
				if($cookie['user_id'] == $user->_id){
					$sessionWrite = $this->writeSession($user->data());
					$ipaddress = $this->request->env('REMOTE_ADDR');
					User::log($ipaddress);
					if(array_key_exists('redirect', $cookie) && $cookie['redirect'] ) {
						$redirect = substr(htmlspecialchars_decode($cookie['redirect']),strlen('http://'.$_SERVER['HTTP_HOST']));
						unset($cookie['redirect']);
					}
					Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
					if (preg_match( '@[^(/|login)]@', $this->request->url ) && $this->request->url) {
						$this->redirect($this->request->url);
					} else {
						$this->redirect($redirect);
					}
				}
			}
		}
	}
	/**
	 * Performs the logout action of the user removing '_id' from session details.
	 */
	public function logout() {
		$success = Session::delete('userLogin');
		$cookie = Session::read('cookieCrumb', array('name' => 'cookie'));
		$cookie['autoLoginHash'] = null;
		Session::delete('cookieCrumb', array('name' => 'cookie'));
		$cookieSuccess = Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
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
		return (Session::write('userLogin', $sessionInfo, array('name'=>'default')));
	}

	/**
	 * Updates the user information.
	 *
	 * @return array
	 */
	public function info() {
		$status = 'default';
		$user = User::getUser();
		if ($this->request->data) {
			$email = $this->request->data['email'];
			$firstname = $this->request->data['firstname'];
			$lastname = $this->request->data['lastname'];
			if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
				if((empty($firstname)) || (empty($lastname))) {
					$status = "name";
				} else {
					$user->legacy = 0;
					$user->reset_token = '0';
					if ($user->save($this->request->data, array('validate' => false))) {
							$info = Session::read('userLogin');
							$info['firstname'] = $this->request->data['firstname'];
							$info['lastname'] = $this->request->data['lastname'];
							Session::write('userLogin', $info, array('name'=>'default'));
							$status = 'true';
						}
				}
			} else {
				$status = "email";
			}
		}
		return compact("user", "status");
	}

	public static function generateToken() {
		return substr(md5(uniqid(rand(),1)), 1, 10);
	}

    public static function randomString($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
    {
        $chars_length = (strlen($chars) - 1);
        $string = $chars{rand(0, $chars_length)};
        for ($i = 1; $i < $length; $i = strlen($string)) {
            $r = $chars{rand(0, $chars_length)};
            if ($r != $string{$i - 1}) $string .=  $r;
        }
        return $string;
    }

	public function reset() {
		$this->_render['layout'] = 'login';
		$success = false;
		if ($this->request->data) {
			$email = strtolower($this->request->data['email']);
			$user = User::find('first', array(
				'conditions' => array(
					'email' => $email
			)));
			if ($user) {
				$token = static::generateToken();
				$user->clear_token = $token;
				$user->reset_token = sha1($token);
				$user->legacy = 0;
				if ($user->save(null, array('validate' => false))) {
					$data = array(
						'user' => $user,
						'email' => $user->email,
						'token' => $token
					);
					Silverpop::send('reset', $data);
					$message = "Your password has been reset. Please check your email.";
					$success = true;
				} else {
					$message = "Sorry your password has not been reset. Please try again.";
				}
			} else {
				$message = "This email doesn't exist.";
			}
		}
		return compact('message', 'success');
	}

	public function invite() {
		$recipient_list = array();
		$user = User::getUser();
		$id = (string) $user->_id;
		// Some documents have arrays, others have strings
		if(is_array($user->invitation_codes)){
			$code = $user->invitation_codes[0];
		} else {
			$code = $user->invitation_codes;
		}
		if ($this->request->data) {
			$rawto = explode(',',$this->request->data['to']);
			$message = $this->request->data['message'];
			foreach ($rawto as $key => $value) {
				preg_match('/<(.*)>/', $value, $matches);
				if ($matches) {
					$to[] = $matches[1];
				} else {
					$to[] = trim($value);
				}
			}
			foreach ($to as $email) {
				$invitation = Invitation::create();
				Invitation::add($invitation, $id, $code, $email);
				$data = array(
					'user' => $user,
					'email' => $email,
					'message' => $message
				);
				Silverpop::send('invite', $data);
			}
			$flashMessage = "Your invitations have been sent";
		}
		$open = Invitation::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user->_id,
				'status' => 'Sent')
		));
		$accepted = Invitation::find('all', array(
			'conditions' => array(
				'user_id' => (string) $user->_id,
				'status' => 'Accepted')
		));

		return compact('user','open', 'accepted', 'flashMessage');
	}

	public function upgrade() {
		$this->_render['layout'] = 'upgrade';
		$user = User::getUser();
		return compact('user');
	}

	/**
	 * Updates the user password.
	 *
	 * @return array
	 */
	public function password() {
		$status = 'default';
		$user = User::getUser(null, $this->sessionKey);
		if ($this->request->data) {
			$oldPass = $this->request->data['password'];
			$newPass = $this->request->data['new_password'];
			$confirmPass = $this->request->data['password_confirm'];
			if ($user->legacy == 1) {
				$status = ($this->authIllogic($oldPass, $user)) ? 'true' : 'false';
			} else {
				$status = (sha1($oldPass) == $user->password) ? 'true' : 'false';
			}
			if (!empty($user->reset_token)) {
				$status = ($user->reset_token == sha1($oldPass) ||
				 $user->password == sha1($oldPass)) ? 'true' : 'false';
			}
			if ($status == 'true') {
				if(($newPass == $confirmPass)){
					if(strlen($confirmPass) > 5){
						$user->password = sha1($newPass);
						$user->legacy = 0;
						$user->reset_token = '0';
						unset($this->request->data['password']);
						unset($this->request->data['new_password']);
						unset($this->request->data['password_confirm']);
						if ($user->save($this->request->data, array('validate' => false))) {
							$info = Session::read('userLogin');
							Session::write('userLogin', $info, array('name'=>'default'));
						}
					} else {
						$status = 'shortpass';
					}
				}else {
					$status = 'errornewpass';
				}
			}
		}
		return compact("user", "status");
	}
}

?>
