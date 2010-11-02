<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\User;
use app\models\Menu;
use app\models\Invitation;
use \lithium\security\Auth;
use \lithium\storage\Session;
use app\extensions\Mailer;


class UsersController extends BaseController {

	/**
	 * Performs basic registration functionality.
	 *
	 * @return string User will be promoted that email is already registered.
	 */
	public function register($invite_code = null) {
		if ($invite_code == 'our365' || $invite_code == 'our365widget' ) {
			$this->_render['template'] = 'our365';
		}
		$message = false;
		$data = $this->request->data;
		if (isset($data) && $this->request->data) {
			$data['emailcheck'] = ($data['email'] == $data['confirmemail']) ? true : false;
			$data['email'] = strtolower($this->request->data['email']);
		}

		$user = User::create($data);
		if ($this->request->data && $user->validates() ) {
			$email = $data['email'];
			$data['password'] = sha1($this->request->data['password']);
			$data['created_date'] = User::dates('now');
			$data['invitation_codes'] = substr($email, 0, strpos($email, '@'));
			$data['invited_by'] = $invite_code;
			$inviteCheck = User::count(array('invitation_codes' => $data['invitation_codes']));
			if ($inviteCheck > 0) {
				$data['invitation_codes'] = array($this->randomString());
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
			if ($user->save($data)) {
				$userLogin = array(
					'_id' => (string) $user->_id,
					'firstname' => $user->firstname,
					'lastname' => $user->lastname,
					'email' => $user->email
				);
				Session::write('userLogin', $userLogin);
				Mailer::send(
					'welcome',
					'Welcome to Totsy!',
					array('name' => $user->firstname, 'email' => $user->email),
					compact('user')
				);
				$ipaddress = $this->request->env('REMOTE_ADDR');
				User::log($ipaddress);
				if ($invite_code == 'keyade') {
					$this->_render['template'] = 'keyade';
				} else {
					$this->redirect('/');
				}
			}
		}
		$this->_render['layout'] = 'login';
		return compact('message', 'user');
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
			$email = strtolower($this->request->data['email']);
			$password = $this->request->data['password'];
			//Grab User Record
			$user = User::lookup($email);
			$redirect = '/';
			if (strlen($password) > 0) {
				if($user){
					if (!empty($user->reset_token)) {
						if (strlen($user->reset_token) > 1) {
							$auth = (sha1($password) == $user->reset_token) ? true : false;
							$redirect = 'account/info';
						}
					}
					if ($user->legacy == 1) {
						$auth = $this->authIllogic($password, $user);
						if ($auth == true) {
							//Write core information to the session and redirect user
							$sessionWrite = $this->writeSession($user->data());
						}
					} else {
						// Try non-legacy user
						$auth = Auth::check("userLogin", $this->request);
					}
					if ($auth) {
						$ipaddress = $this->request->env('REMOTE_ADDR');
						User::log($ipaddress);
						if ($this->request->url != 'login' && $this->request->url) {
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
			if ($user->legacy == 1) {
				$status = ($this->authIllogic($oldPass, $user)) ? 'true' : 'false';
			} else {
				$status = (sha1($oldPass) == $user->password) ? 'true' : 'false';
			}
			if (!empty($user->reset_token)) {
				$status = ($user->reset_token == sha1($oldPass) || $user->password == sha1($oldPass)) ? 'true' : 'false';
			}
			if ($status == 'true') {
				$user->password = sha1($newPass);
				$user->legacy = 0;
				$user->reset_token = '0';
				unset($this->request->data['password']);
				unset($this->request->data['new_password']);
				if ($user->save($this->request->data, array('validate' => false))) {
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
		$this->_render['layout'] = 'login';
		$success = false;
		if ($this->request->data) {
			$user = User::find('first', array(
				'conditions' => array(
					'email' => $this->request->data['email']
			)));
			if ($user) {
				$token = $this->generateToken();
				$user->reset_token = sha1($token);
				$user->legacy = 0;
				if ($user->save(null, array('validate' => false))) {
					Mailer::send(
						'password',
						'Totsy Password Reset',
						array('name' => $user->firstname, 'email' => $user->email),
						compact('token', 'user')
					);
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

	protected function generateToken() {
        return substr(md5(uniqid(rand(),1)), 1, 10);
    }


	// Generate a random character string
	protected function randomString($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		$chars_length = (strlen($chars) - 1);
		$string = $chars{rand(0, $chars_length)};
		for ($i = 1; $i < $length; $i = strlen($string)) {
			$r = $chars{rand(0, $chars_length)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		return $string;
	}

	public function invite() {
		$recipient_list = array();
		$user = User::getUser();
		$id = (string) $user->_id;
		$code = $user->invitation_codes[0];
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
				Mailer::send(
					'invite',
					'You have been invited to Totsy.com!',
					array('name' => '', 'email' => $email),
					compact('user', 'message')
				);
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
}

?>
