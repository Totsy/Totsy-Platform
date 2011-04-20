<?php

namespace app\controllers;

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
				if (isset($data['password'])) {
					// New user, need to register here
					$user['firstname'] = $data['fname'];
					$user['lastname'] = $data['lname'];
					$user['email'] = strtolower($data['email']);
					$user['zip'] = $data['zip'];
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
			if (($gdata)) {
				$affiliate = Affiliate::storeSubAffiliate($gdata, $affiliate);
				if (array_key_exists('redirect', $gdata)) {
					$urlredirect = parse_url(htmlspecialchars_decode(urldecode($gdata['redirect'])), PHP_URL_PATH);
				}
			}
			$cookie['affiliate'] = $affiliate;
			Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
			if (Session::check('userLogin', array('name' => 'default'))) {
				$userlogin = Session::read('userLogin');
				Affiliate::linkshareCheck($userlogin['_id'], $affiliate, $cookie);
				$this->redirect($urlredirect);
			}
			UsersController::facebookLogin($affiliate, $cookie, $ipaddress);
			if (($pdata)) {
				$data['email'] = strtolower($pdata['email']);
			//	$data['firstname'] = $pdata['firstname'];
			//	$data['lastname'] = $pdata['lastname'];
				$data['email'] = htmlspecialchars_decode(strtolower($pdata['email']));
			//	$data['zip'] = $pdata['zip'];
				$data['confirmemail'] = htmlspecialchars_decode(strtolower($pdata['email']));
				$data['password'] = $pdata['password'];
				$data['terms'] = (boolean) $pdata['terms'];
				$data['invited_by'] = $affiliate;
				extract(UsersController::registration($data));
				if ($saved) {
					$message = $saved;
					$userLogin = array(
						'_id' => (string) $user->_id,
						'firstname' => $user->firstname,
						'lastname' => $user->lastname,
						'zip' => $user->zip,
						'email' => $user->email
					);
					Session::write('userLogin', $userLogin, array('name'=>'default'));
					Affliate::linkshareCheck($userLogin['_id'], $affiliate, $cookie);
					User::log($ipaddress);
					Session::write('pixel',$pixel, array('name'=>'default'));
					$this->redirect($urlredirect);
				}
			}
		}
		$this->_render['layout'] = 'login';
		return compact('message', 'user');
	}
}

?>