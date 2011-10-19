<?php

namespace app\controllers;

use app\extensions\Mailer;
use app\models\Affiliate;
use app\models\User;
use MongoDate;
use lithium\storage\Session;
use li3_facebook\extension\FacebookProxy;

class AffiliatesController extends BaseController {

	/**
	* Affiliate registration from remote POST.
	* @params string $code
	* @return boolean $success
	**/
	public function registration($code = NULL) {
		$success = false;
		$message = '';
		$errors = 'Affiliate does not exists';

		if ($code) {
			$count = Affiliate::count(array('conditions' => array('invitation_codes' => $code)));
			if ( $count == 0 ) {
				return compact('success', 'errors');
			}

			if ($this->request->data){
				$data = $this->request->data;
				if (isset($this->request->query)){
					$query = $this->request->query;
				}
				$genpasswd = false;
				if (isset($query) && isset($query['genpswd']) && $query['genpswd'] == 'true'){
					$genpasswd = true;
				}
				if (isset($data['email']) && $genpasswd==true) {
					$token = User::generateToken();
					$user['clear_token'] = $token;
					$user['reset_token'] = sha1($token);
					$user['legacy'] = 0;
					$data['password'] = $token . '@' . $user['reset_token'];
				}
				if (isset($data['password'])) {
					// New user, need to register here
					if (array_key_exists('fname',$data)){
					    $user['firstname'] = $data['fname'];
					}
					if (array_key_exists('lname',$data)) {
					    $user['lastname'] = $data['lname'];
					}
					$user['email'] = strtolower($data['email']);
					if (array_key_exists('zip', $data) ){
					    $user['zip'] = $data['zip'];
					}
					$user['confirmemail'] = strtolower($data['email']);
					$user['password'] = $data['password'];
					$user['terms'] = "1";
					$user['invited_by'] = $code;
					extract(UsersController::registration($user));

					$success = $saved;
					$errors = $user->errors();
				}
			}
			$this->render(array('data'=>compact('success','errors'), 'layout' =>false));
		}
	}

	/**
	*	Affiliate-user invite register
	*   @params $affiliate
	**/
	public function register($affiliate = NULL) {
		$pdata = $this->request->data;
		$message = false;
		$user = User::create();
		$urlredirect = '/sales';
		$cookie = Session::read('cookieCrumb',array('name'=>'cookie'));
		$ipaddress = $this->request->env('REMOTE_ADDR');
		if (($affiliate)) {
			$pixel = Affiliate::getPixels('after_reg', $affiliate);
			$gdata = $this->request->query;
			$params = $this->request->params;
			if (($gdata)) {
				$affiliate = Affiliate::storeSubAffiliate($gdata, $affiliate);
				if (array_key_exists('redirect', $gdata)) {
					$urlredirect = parse_url(htmlspecialchars_decode(urldecode($gdata['redirect'])), PHP_URL_PATH);
				}
			}

			$cookie['landing_url'] = $_SERVER['REQUEST_URI'];
			$cookie['affiliate'] = $affiliate;
			if ($cookie['affiliate'] === 'keyade') {
			    $cookie['keyadeId'] = $params['args'][1];
			}

			Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
			if (Session::check('userLogin', array('name' => 'default'))) {
				$userlogin = Session::read('userLogin');
				Affiliate::linkshareCheck($userlogin['_id'], $affiliate, $cookie);
				$this->redirect($urlredirect);
			}
			$result = UsersController::facebookLogin($affiliate, $cookie, $ipaddress);
            extract($result);
            if (!$success) {
                if (!empty($userfb)) {
                    if ( !preg_match( '/@proxymail\.facebook\.com/', $fbuser['email'] )) {
                        $user->email = $userfb['email'];
                        $user->confirmemail = $userfb['email'];
                    }
                }
            }

			if (($pdata)) {
				$data['email'] = htmlspecialchars_decode(strtolower($pdata['email']));
				$data['confirmemail'] = htmlspecialchars_decode(strtolower($pdata['email']));
				$data['password'] = $pdata['password'];
				$data['terms'] = (boolean) $pdata['terms'];
				$data['invited_by'] = $affiliate;
				if (!empty($userfb)) {
				    $data['facebook_info'] = $userfb;
			        $data['firstname'] = $userfb['first_name'];
			        $data['lastname'] = $userfb['last_name'];
				}
				switch ($affiliate) {
                    case 'our365':
                    case 'our365widget':
                     //   $this->_render['template'] = 'our365';
                        break;
                    case 'keyade':
                      //  $this->_render['template'] = 'keyade';
                        if(count($params['args'] > 1)){
                            $data['keyade_user_id'] = $params['args'][1];
                        } else {
                            $data['keyade_user_id'] = 0;
                        }
                }
				extract(UsersController::registration($data));
				if ($saved) {
					$message = $saved;
					Affiliate::linkshareCheck($user->_id, $affiliate, $cookie);
					$this->writeSession($user->data());
					$cookie['user_id'] = $user->_id;
					Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
		            Session::write('pixel', $pixel, array('name' => 'default'));
					Affiliate::linkshareCheck($user->_id, $affiliate, $cookie);
					User::log($ipaddress);
					$this->redirect($urlredirect);
				}
			}
		}
		$this->_render['layout'] = 'login';
		return compact('message', 'user', 'userfb');
	}
}
?>