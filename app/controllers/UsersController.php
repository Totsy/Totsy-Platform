<?php

namespace app\controllers;

use app\models\User;
use totsy_common\models\Menu;
use app\models\Affiliate;
use app\models\Invitation;
use lithium\security\Auth;
use lithium\storage\Session;
use app\extensions\Mailer;
use app\extensions\Keyade;
use MongoDate;
use li3_facebook\extension\FacebookProxy;

class UsersController extends BaseController {

	public $sessionKey = 'userLogin';

	/**
	 * Instances
	 * @var array
	 */
	protected static $_instances = array();

	/**
	 * Class dependencies.
	 * @var array
	 */
	protected $_classes = array(
		'mailer' => 'app\extensions\Mailer'
	);

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

		$parsedURI = parse_url($this->request->env("REQUEST_URI"));
		$currentURI = $parsedURI['path'];

		$eventName = "";

		if (preg_match("(/sale)", $currentURI)) {
		    $URIArray = explode("/", $currentURI);
		    $eventName = $URIArray[2];
		}

		if ($eventName) {
           //write event name to the session
           Session::write( "eventFromEmailClick", $eventName, array("name"=>"default"));
       }

       //redirect to login ONLY if the user is coming from an email
       if ($this->request->query["gotologin"]=="true") {
           $this->redirect("/login");
       }

		$this->_render['layout'] = 'login';
		$message = false;
		$data = $this->request->data;
		$this->autoLogin();

		/*
		* redirects to the affiliate registration page if the left the page
		* and then decided to register afterwards.
		*/

		$cookie = Session::read('cookieCrumb', array('name' => 'cookie'));
		if($cookie && preg_match('(/a/)', $cookie['landing_url'])){
			return $this->redirect($cookie['landing_url']);
		}
		$referer = parse_url($this->request->env('HTTP_REFERER')) + array('host' => null);
		if ($referer['host']==$this->request->env('HTTP_HOST') && preg_match('(/sale/)',$referer['path'])){
			Session::write('landing',$referer['path'],array('name'=>'default'));
		}
		unset($referer);
		if (isset($data) && $this->request->data) {
			$data['emailcheck'] = ($data['email'] == $data['confirmemail']) ? true : false;
			$data['email'] = strtolower($this->request->data['email']);
			$data['email_hash'] = md5($data['email']);
		}
		$user = User::create($data);
		if ($this->request->data && $user->validates() ) {
			$email = $data['email'];
			$data['password'] = sha1($this->request->data['password']);
			$data['created_date'] = new MongoDate();
			$data['invitation_codes'] = array(substr($email, 0, strpos($email, '@')));
			$data['invited_by'] = $invite_code;
			$inviteCheck = User::count(array('invitation_codes' => $data['invitation_codes']));
			if ($inviteCheck > 0) {
				$data['invitation_codes'] = array(static::randomString());
			}

			/* links up inviter with the invitee and sends an email notification */

			Invitation::linkUpInvites($invite_code, $email);

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
				Session::write('userLogin', $userLogin, array('name' => 'default'));
				$cookie['user_id'] = $user->_id;
				Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
				#Remove Temporary Session Datas**/
				User::cleanSession();
				$data = array(
					'user' => $user,
					'email' => $user->email
				);
				$mailer = $this->_classes['mailer'];
				$mailer::send('Welcome_Free_Shipping', $user->email);
				$mailer::addToMailingList($data['email']);
				$mailer::addToSuppressionList($data['email']);
				$ipaddress = $this->request->env('REMOTE_ADDR');
				User::log($ipaddress);

				$landing = null;
				if (Session::check('landing')){
					$landing = Session::read('landing');
				}
				if (!empty($landing)){
					Session::delete('landing',array('name'=>'default'));
					return $this->redirect($landing);
					unset($landing);
				} else {
					return $this->redirect('/sales');
				}

			}
		}
		
		if($this->request->is('mobile')){
		 	$this->_render['layout'] = 'mobile_login';
		 	$this->_render['template'] = 'mobile_register';
		} else {
			//$this->_render['layout'] = 'login';
		}
		
		if ($this->request->data && !$user->validates() ) {
			$message = '<div class="error_flash">Error in registering your account</div>';
		}
		if($this->request->is('mobile')){
		 	$this->_render['layout'] = 'mobile_login';
		 	$this->_render['template'] = 'mobile_register';
		} else {
			//$this->_render['layout'] = 'login';
		}
		
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
				$data['email_hash'] = md5($data['email']);
				$user = User::create($data);
				if ($user->validates()) {
					$email = $data['email'];
					$data['password'] = sha1($data['password']);
					$data['created_date'] = User::dates('now');
					$data['invitation_codes'] = array(substr($email, 0, strpos($email, '@')));
					$inviteCheck = User::count( array(
							'invitation_codes' => $data['invitation_codes']
							));
					if ($inviteCheck > 0) {
						$data['invitation_codes'] = array(static::randomString());
					}
					if ($saved = $user->save($data)) {
						$mail_template = 'Welcome_Free_Shipping';
						$params = array();

						$data = array(
							'user' => $user,
							'email' => $user->email
						);

						if (isset($user['clear_token'])) {
							$mail_template = 'Welcome_auto_passgen';
							$params['token'] = $user['clear_token'];
						}
						Mailer::send($mail_template, $user->email,$params);

						$args = array();
						if (!empty($user->firstname)) $args['name'] = $user->firstname;
						if (!empty($user->lastname)) $args['name'] = $args['name'] . $user->lastname;
						if (!empty($user->invited_by)) {
							$affiliate_cusror = Affiliate::collection()->find(array('invitation_codes'=>$user->invited_by));
							if ($affiliate_cusror->hasNext()){
								$affiliate = $affiliate_cusror->getNext();
								$args['source'] = $affiliate['name'];
								unset($affiliate);
							}
							unset($affiliate_cusror);
						}

						Mailer::addToMailingList($data['email'],$args);
						Mailer::addToSuppressionList($data['email']);

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


		//redirect to the right email if the user is coming from an email
		//the session writes this variable on the register() method
		//it writes it THERE because that is the method currently serving our homepage
		$this->autoLogin();
		if ($this->request->data) {

			$email = trim(strtolower($this->request->data['email']));
			$password = trim($this->request->data['password']);
			$this->request->data['password'] = trim($this->request->data['password']);
			$this->request->data['email'] = trim($this->request->data['email']);
			//Grab User Record
			$user = User::lookup($email);
			//redirect for people coming from emails
			if ( Session::read("eventFromEmailClick", array("name"=>"default"))) {
				//$redirect = "/sale/schoolbags-for-kids";
				$redirect = "/sale/".Session::read("eventFromEmailClick", array("name"=>"default"));
			} else {
				$redirect = '/sales';
			}
			if ($user->deactivated) {
				$message = '<div class="error_flash">Your account has been deactivated.  Please contact Customer Service at 888-247-9444 to reactivate your account</div>';
			} else if (strlen($password) > 0) {
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
            			//$userInfo = Session::read('userLogin');
            			$cookie['user_id'] = $user['_id'];
            			if(array_key_exists('redirect', $cookie) && $cookie['redirect'] ) {
							$redirect = substr(htmlspecialchars_decode($cookie['redirect']),strlen('http://'.$_SERVER['HTTP_HOST']));
							unset($cookie['redirect']);
						}

						$landing = null;
						if (Session::check('landing')){
							$landing = Session::read('landing');
							Session::delete('landing',array('name'=>'default'));
							if (empty($landing)){
								$landing = $redirect;
							}
						} else if (preg_match( '@[^(/|login)]@', $this->request->url ) && $this->request->url) {
							$landing = $this->request->url;
						} else {
							$landing  = $redirect;
						}

            			Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
						User::rememberMeWrite($this->request->data['remember_me']);
						/**Remove Temporary Session Datas**/
						User::cleanSession();
						/***/

						return $this->redirect($landing);
					} else {
						$message = '<div class="error_flash">Login Failed - Please Try Again</div>';
					}
				} else {
					$message = '<div class="error_flash">Login Failed - No Record Found</div>';
				}
			} else {
				$message = '<div class="error_flash">Login Failed - Your Password Is Blank</div>';
			}


		}
		//detect mobile and make the view switch
		if($this->request->is('mobile')){
		 	$this->_render['layout'] = 'mobile_login';
		 	$this->_render['template'] = 'mobile_login';
		} else {
			$this->_render['layout'] = 'login';
		}

		return compact('message', 'fbsession', 'fbconfig');
	}

	protected function autoLogin() {

		$redirect = '/sales';
		$ipaddress = $this->request->env('REMOTE_ADDR');
		$cookie = Session::read('cookieCrumb', array('name' => 'cookie'));

		$result = static::facebookLogin(null, $cookie, $ipaddress);

		extract($result);

		$fbCancelFlag = false;

		if (array_key_exists('fbcancel', $this->request->query)) {
			$fbCancelFlag = $this->request->query['fbcancel'];
		}

		if (!$success) {
			if (!empty($userfb)) {
				$self = static::_object();
				if(!$fbCancelFlag) {
					return $this->redirect('/register/facebook');
				}
			}
		}

		if(preg_match( '@^[(/|login|register)]@', $this->request->url ) && $cookie && array_key_exists('autoLoginHash', $cookie)) {
			$user = User::find('first', array(
				'conditions' => array('autologinHash' => $cookie['autoLoginHash'])));
			if($user) {
				if ($user->deactivate) {
					return;
				} else if($cookie['user_id'] == $user->_id){
					Session::write('userLogin', $user->data(), array('name'=>'default'));
					User::log($ipaddress);
					if(array_key_exists('redirect', $cookie) && $cookie['redirect'] ) {
						$redirect = substr(htmlspecialchars_decode($cookie['redirect']),strlen('http://'.$_SERVER['HTTP_HOST']));
						unset($cookie['redirect']);
					}
					Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
					if (preg_match( '@[^(/|login|register)]@', $this->request->url ) && $this->request->url) {
						return $this->redirect($this->request->url);
					} else {
						return $this->redirect($redirect);
					}
				} else {
					$cookie['autoLoginHash'] = null;
					Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
				}
			}
		}
	}

	/**
	 * Performs the logout action of the user removing '_id' from session details.
	 */

	public function logout() {

		FacebookProxy::destroySession();
		$loginInfo = Session::read('userLogin');
		$user = User::collection();
		$user->update(
			array('email' => $loginInfo['email']),
			array('$unset' => array('autologinHash' => 1))
			);
		$success = Session::delete('userLogin');
		$cookie = Session::read('cookieCrumb', array('name' => 'cookie'));

		unset($cookie['autoLoginHash']);

		User::cleanSession();
		Session::delete('cookieCrumb', array('name' => 'cookie'));
		$cookieSuccess = Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));

		return $this->redirect(array('action' => 'login'));
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
	 * Updates the user information.
	 *
	 * @return array
	 */
	public function info() {

		$status = 'default';
		$user = User::getUser();


		$linked = (empty($user->facebook_info) ? false : true);
		$connected = false;
		if ($linked) {
			$userId = $user->facebook_info->id;
			$connected = true;
			try {

				$accessToken = FacebookProxy::getAccessToken();

				$authCheck = FacebookProxy::api("/$userId?access_token=$accessToken");
				$connected = (!empty($authCheck['email'])) ? true : false;
			} catch (\Exception $e) {
				$connected = false;
			}
		}


		if(FacebookProxy::getUser()){
			$fbsession = FacebookProxy::getUser();
		}

		if ($fbsession && $linked == false) {
			try {
				$userfb = FacebookProxy::api($fbsession);
				$check = User::find('first', array(
					'conditions' => array(
							'facebook_info.id' => $userfb['id']
				)));
			} catch (\Exception $e) {
				$connected = false;
			}
			if (empty($check)) {
				$user->facebook_info = $userfb;
				$user->save(null, array('validate' => false));
				$connected = true;
			} else {
				$status = 'badfacebook';
			}
		}
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
					$user->email_hash = md5($user->email);
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
		if($this->request->is('mobile')){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_info';
		}
		return compact('user', 'status', 'connected', 'failed', 'userfb');
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
		if($this->request->is('mobile')){
		 	$this->_render['layout'] = 'mobile_login';
		 	$this->_render['template'] = 'mobile_reset';
		} else {
			$this->_render['layout'] = 'login';
		}
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
				$user->email_hash = md5($user->email);
				if ($user->save(null, array('validate' => false))) {
					$mailer = $this->_classes['mailer'];
					$mailer::send('Reset_Password', $user->email, array('token' => $token));
					Mailer::send('Reset_Password', $user->email, array('token' => $token));
					$message = '<div class="success_flash">Your password has been reset. Please check your email.</div>';
					$success = true;
				} else {
					$message = '<div class="error_flash">Sorry your password has not been reset. Please try again.</div>';
				}
			} else {
				$message =  '<div class="error_flash">This email doesn\'t exist.</div>';
			}
			
		}
		if($this->request->is('mobile')){
		 	$this->_render['layout'] = 'mobile_login';
		 	$this->_render['template'] = 'mobile_reset';
		} else {
			$this->_render['layout'] = 'login';
		}
		return compact('message', 'success');
	}

	public function invite() {
		$recipient_list = array();
		$user = User::getUser();
		$id = (string) $user->_id;
		// Some documents have arrays, others have strings

		if(is_object($user->invitation_codes) && get_class($user->invitation_codes) == "lithium\data\collection\DocumentArray"){
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
				$args = array(
					'firstname' => $user->firstname,
					'message' => $message,
					'email_from' => $user->email,
					'domain' => 'http://'.$this->request->env("HTTP_HOST"),
					'invitation_codes' => $code
				);
				$mailer = $this->_classes['mailer'];
				$mailer::send('Friend_Invite', $email, $args);
			}
			$flashMessage = '<div class="success_flash">Your invitations have been sent</div>';
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

		$pixel = Affiliate::getPixels('invite', 'spinback');
		$spinback_fb = Affiliate::generatePixel('spinback', $pixel,
			                                            array('invite' => $_SERVER['REQUEST_URI'])
			                                            );
		if($this->request->is('mobile')){
			$this->_render['layout'] = 'mobile_main';
			$this->_render['template'] = 'mobile_invite';
		}
		return compact('user','open', 'accepted', 'flashMessage', 'spinback_fb');
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
		if($this->request->is('mobile')){
		 	$this->_render['layout'] = 'mobile_main';
		 	$this->_render['template'] = 'mobile_password';
		}
		return compact("user", "status");
	}

	/**
	 * Register with a facebook account
	 * @return compact
	 */
	public function fbregister() {
		$message = null;
		$user = null;
		$fbuser = FacebookProxy::api(FacebookProxy::getUser());
		$user = User::create();

		if ( !preg_match( '/@proxymail\.facebook\.com/', $fbuser['email'] )) {
			$user->email = $fbuser['email'];
			$user->email_hash = md5($user->email);
			$user->confirmemail = $fbuser['email'];
		}
		$this->_render['layout'] = 'login';
		if ($this->request->data) {
			$data = $this->request->data;
			$data['facebook_info'] = $fbuser;
			$data['firstname'] = $fbuser['first_name'];
			$data['lastname'] = $fbuser['last_name'];
			static::registration($data);

			$landing = null;
			if (Session::check('landing')){
				$landing = Session::read('landing');
			}
			if (!empty($landing)){
				Session::delete('landing',array('name'=>'default'));
				$this->redirect($landing);
				unset($landing);
			} else {
				$this->redirect('/sales');
			}
		}

		return compact('message', 'user', 'fbuser');
	}

	/**
	 * Auto login a user if the facebook session has been set.
	 *
	 * If the user already exists in our system redirect them to sales.
	 * If not then return false and the user facebook information to the
	 * function who called it
	 *
	 * @param string $affiliate - Affiliate string
	 * @param string $cookie - The affiliate cookie set from affiliate
	 * @see Affiliates::register()
	 * @see FacebookProxy::api()
	 */

	public static function facebookLogin($affiliate = null, $cookie = null, $ipaddress = null) {
		$self = static::_object();

		//If the users already exists in the database
		$success = false;
		$userfb = array();

		if ($self->fbsession) {

			$userfb = FacebookProxy::api($self->fbsession);

			$user = User::find('first', array(
				'conditions' => array(
					'$or' => array(
						array('email' => strtolower($userfb['email'])),
						array('facebook_info.id' => $userfb['id'])
			))));

			if ($user) {

			//$userfb = FacebookProxy::getUser();
			$user->facebook_info = $userfb;
			$user->save(null, array('validate' => false));

			$sessionWrite = $self->writeSession($user->data());

			Affiliate::linkshareCheck($user->_id, $affiliate, $cookie);

			User::log($ipaddress);

			$landing = null;

			if (Session::check('landing')){
				$landing = Session::read('landing');
			}

			if (!empty($landing)) {
			    Session::delete('landing',array('name'=>'default'));
			    $self->redirect($landing, array('exit' => true));
			    unset($landing);
			} else {
			    $self->redirect("/sales", array('exit' => true));
			}

			}
		}

		return compact('success', 'userfb');
	}

	protected static function &_object() {
		$class = get_called_class();
		if (!isset(static::$_instances[$class])) {
			static::$_instances[$class] = new $class();
		}
		return static::$_instances[$class];
	}

}

?>