<?php

namespace app\controllers;

use app\extensions\Mailer;
use app\models\Affiliate;
use app\models\User;
use MongoDate;
use lithium\storage\Session;
use li3_facebook\extension\FacebookProxy;
use app\models\Invitation;

class AffiliatesController extends BaseController {

	/**
	* Affiliate registration from remote POST.  Some affiliates might be behind https such as
	* bamboo due since we return sensitive information to them.
	*
	* @param (string) $code : affiliate code
	* @return (boolean) $success
	* @see app/models/User::lookUp()
	* @see app/models/User::retrieveInvitationCode()
	* @see app/models/Invitation::linkUpInvites()
	**/
	public function registration($code = NULL) {
			
		$success = false;
		$message = '';
		$errors = '';
		$_id = null;
        $this->_render['layout'] = false;
		if ($code) {
			$count = Affiliate::count(array('conditions' => array('invitation_codes' => $code)));
			if ( $count == 0 ) {
			    $errors = 'Affiliate does not exists';
				return compact('success', 'errors');
			}

			if ($this->request->data){
				$data = $this->request->data;
				$query = $this->request->query;
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
                        if (array_key_exists('fname', $data)){
                            $user['firstname'] = trim($data['fname']);
                        }
                        if (array_key_exists('lname', $data)) {
                            $user['lastname'] = trim($data['lname']);
                        }
                        $user['email'] = trim(strtolower($data['email']));
                        if (array_key_exists('zip', $data) ){
                            $user['zip'] = $data['zip'];
                        }
                        $user['confirmemail'] = trim(strtolower($data['email']));
                        $user['password'] = $data['password'];
                        $user['terms'] = "1";
                        extract(UsersController::registration($user));
                        if ($saved) {
                            if ($code == 'bamboo') {
                                 if ($this->request->query) {
                                   $inviter_id = $this->request->query['referredById'];
                                   $facebk_id = $this->request->query['fbid'];
                                   $fbinfo = User::fbAccessCheck($facebk_id);
                                   if (!empty($fbinfo) && User::fbTotsyLinkCheck($facebk_id)) {
                                     User::fbTotsyLinkUp((string) $user->_id, $fbinfo);
                                   }
                                   if ($inviter_id) {
                                     $user->invited_by = User::retrieveInvitationCode($inviter_id);
                                     $invite_code = Invitation::retrieveInviteCode($inviter_id);
                                     $email = $user['email'];
                                     Invitation::linkUpInvites($invite_code, $email);
                                   }
                                }
                            $_id = (string) $user->_id;
                            $this->set(compact('_id'));
                            }
                            if(empty($user->invited_by)) {
                                $user->invited_by = $code;
                            }
                            $user->save(null,array('validate' => false));
                    	}
                        $success = $saved;
                        $errors = $user->errors();

                    if ($code == 'bamboo' && !empty($errors) &&
						array_key_exists('email', $errors) &&
						($errors['email'][0] == "This email address is already registered"
					)) {
						$user = User::lookup(trim(strtolower($data['email'])));
						$_id = (string) $user->_id;
                        $this->set(compact('_id'));
					}
                } //end of password if
			}
		}
		$this->set(compact('success','errors', '_id'));
	}

	/**
	*	Affiliate-user invite register
	*   @params $affiliate
	**/

	public function register($affiliate = NULL) {
		//ini_set("display_errors", 1);
		
		//affiliate category name
		$categoryName = "";
		//affiliate name
		$affiliateName = "";
		//for affiliate background images
		$affBgroundImage = "";
		
		if (isset($this->request->query['a']) || preg_match('/^[a-z_]+$/', $this->request->query['a'])) {
		
       		$categoryName = trim($this->request->params['args'][1]);
			$affiliateName = trim($this->request->params['args'][0]); 
			$backgroundImage = "";
			
			$affiliate = $affiliateName;
							
			$getAff = Affiliate::find('first',
				array('conditions' => array(
					'name'=> $affiliateName)
			));
			
			foreach($getAff['category'] as $record=>$value) {
				$catRecord = $value->data();
				
				if($catRecord['name']==$categoryName){
					$affBgroundImage = $catRecord['background_image'];
					break;
				}	
			}			
		}
				
		$pdata = $this->request->data;
		
		$message = false;
		$user = User::create();
		$urlredirect = '/sales';
		$cookie = Session::read('cookieCrumb',array('name'=>'cookie'));
		$ipaddress = $this->request->env('REMOTE_ADDR');
		
		//mamasource regsitation
		switch($this->request->env("HTTP_HOST")) {
		    case "mamasource.totsy.com":
		    case "evan.totsy.com":
		        $affiliate = "mamasource";
		    	break;
		    default:
		        break;
		}

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
		if($this->request->is('mobile')){
			$this->_render['layout'] = 'mobile_main';
			$this->_render['template'] = 'mobile_register';
		} else {
			$this->_render['layout'] = 'login';
		}	
		return compact('message', 'user', 'userfb','categoryName','affiliateName','affBgroundImage','affiliateName');
	}
}
?>
