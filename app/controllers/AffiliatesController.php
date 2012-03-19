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

			if ($this->request->data) {
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
		
		if ($affiliate) {
			$pixel = Affiliate::getPixels('after_reg', $affiliate);
			$gdata = $this->request->query;
			$params = $this->request->params;
			if ($gdata) {
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

			$fbCancelFlag = false;
			if (array_key_exists('fbcancel', $this->request->query)) {
				$fbCancelFlag = $this->request->query['fbcancel'];
			}

			if ((!empty($userfb) && !$fbCancelFlag) || $pdata) {
				$affiliateData = array('invited_by' => $affiliate);
				if (preg_match('/^keyade/i', $affiliate)) {
					$affiliateData['keyade_user_id'] = 0;
				    if (array_key_exists('clickId', $gdata)){
				        $affiliateData['keyade_user_id'] = $gdata['clickId'];
				    } else if(count($params['args']) > 1 && is_numeric($params['args'][1])){
                        $affiliateData['keyade_user_id'] = $params['args'][1];
                    }
				}

				// create a new Totsy account using Facebook user data
				if (!empty($userfb) && !$fbCancelFlag) {
					
					if(Session::read('layout', array('name' => 'default'))!=='mamapedia'){
					//execute and assign return value to tmp
					$tmp = extract(UsersController::fbregister($affiliateData));
						
						//check if any data was returned and show error msg on register form
						if($tmp==0) { 
							$message = "<div class='error_flash'>Facebook.com appears to be having issues. Please try our native registration form below in the meantime.</div>";
							
							if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia') {
								$this->_render['layout'] = 'mobile_main';
								$this->_render['template'] = 'mobile_register';
							} else {
								$this->_render['layout'] = 'login';
							}
													
							return compact('message');
						}
					} else {
						$data = array(
							'email'					=> $userfb['email'],
							'confirmemail'			=> $userfb['email'],
							'password'				=> UsersController::randomString(),
							'requires_set_password' => true,
							'terms'					=> true,
							'facebook_info'			=> $userfb,
							'firstname'				=> $userfb['first_name'],
							'lastname'				=> $userfb['last_name']);
											
						extract(UsersController::registration($data + $affiliateData));						
					}					

				// create a new Totsy account using form data
				} else if($pdata) {				
				
					if( isset($this->request->query['fboneclick']) && $this->request->query['fboneclick']==1 ) {
						$pdata['invited_by']="facebookoneclick";	
					}
				
					extract(UsersController::registration($pdata + $affiliateData));
				}
				

				if ($saved) {
				
					$message = $saved;
					Affiliate::linkshareCheck($user->_id, $affiliate, $cookie);
					$this->writeSession($user->data());
					$cookie['user_id'] = $user->_id;
					Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
		            Session::write('pixel', $pixel, array('name' => 'default'));
					Affiliate::linkshareCheck($user->_id, $affiliate, $cookie);
					User::log($ipaddress);

				}
				$this->redirect($urlredirect);
			}
		}
						
		if($this->request->is('mobile') && Session::read('layout', array('name' => 'default'))!=='mamapedia') {
			$this->_render['layout'] = 'mobile_main';
			$this->_render['template'] = 'mobile_register';
		} else {
			$this->_render['layout'] = 'login';
		}			
				
		return compact('message', 'user', 'userfb','categoryName','affiliateName','affBgroundImage','affiliate');

	}
}
?>
