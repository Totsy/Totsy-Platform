<?php

namespace app\models;

use lithium\data\Connections;
use lithium\storage\Session;
use lithium\storage\session\adapter\Cookie;
use MongoDate;
use MongoId;
use lithium\util\Validator;
use li3_facebook\extension\FacebookProxy;

/**
 * The User Model is a direct link to the MongoDb users collection.
 *
 * All the basic information regarding the user is stored in this collection.
 *
 * General Schema:
 *
 * {{{
 * {
 *		_id: MongoId,
 * 		firstname: String (required),
 * 		lastname: String (required),
 * 		email: String (required),
 * 		password: String (required),
 * 		invitation_codes: mixed (array, string) (required),
 * 		invited_by: String,
 * 		lastip: String,
 * 		lastlogin: MongoDate,
 * 		logincounter: Int,
 * 		purchase_count: Int,
 * 		created_date: MongoDate,
 * 		total_credit: Float,
 * 		active: mixed (1/0),
 * 		legacy: mixed (1/0),
 * 		salt: String (From Legacy Users),
 * 		superadmin: Boolean,
 * 		affiliate: (1/0)
 * }
 * }}}
 *
 * TODO: Affiliate, active, and legacy should all be changed to the Boolean type
 */
class User extends Base {

	public $validates = array(
	/*	'firstname' => array(
			'notEmpty', 'required' => false, 'message' => 'Please add a first name'
		),
		'lastname' => array(
			'notEmpty', 'required' => false, 'message' => 'Please add a last name'
		),
			'zip' => array(
				'notEmpty', 'required' => false, 'message' => 'Please add a zip code'
		),*/
		'email' => array(
			array('simpleEmail', 'message' => 'Invalid e-mail address. It must contain only letters, numbers, underscores (_), hyphens (-), and periods (.)'),
			array('notEmpty', 'required' => true, 'message' => 'Please add an email address'),
			array('isUniqueEmail', 'message' => 'This email address is already registered'),
			array('isEmailFacebookLegal', 'required' => true, 'message' => 'Your Facebook email is not set to be shared, please enter a real email address')
		),
		'password' => array(
			'notEmpty', 'required' => true, 'message' => 'Please submit a password'
		),
		'terms' => array(
			'notEmpty', 'required' => true, 'message' => 'Please accept terms'
		),
		'confirmemail' => array(
			array('notEmpty', 'required' => true, 'message' => 'Please confirm your email address')
		),
		'emailcheck' => array(
			array('isEmailMatch', 'message' => 'Emails don\'t match. Please confirm email.')
		)
	);

	public static function __init(array $options = array()) {
		parent::__init($options);

		// a simplified e-mail validation rule, that requires a limited set of
		// characters (alphanumeric, underscore, hyphens and periods) separated
		// by a single @ symbol
		Validator::add('simpleEmail', function($value) {
			@list($user, $domain) = explode('@', $value);

			$relaxedEmailDomains = array(
				'totsy.com'
			);

			// use the standard e-mail validator for a specific set of domains
			if (in_array($domain, $relaxedEmailDomains)) {
				return Validator::isEmail($value);

			// use the strict e-mail syntax
			} else {
				return (bool) preg_match_all('/^[\w\-\.]+@[\w\-\.]+$/', $value, $matches);
			}
		});

		Validator::add('isEmailMatch', function ($value) {
			return ($value ==  true) ? true : false;
		});

		Validator::add('isEmailFacebookLegal', function ($value) {
			$facebooklegal = preg_match('/@proxymail\.facebook\.com/', $value);
			return ($facebooklegal ==  true) ? false : true;
		});

		Validator::add('isUniqueEmail', function ($value) {
			$email = User::count(array('email' => "$value"));
			return (empty($email)) ? true : false;
		});
	}

	public static function collection() {
		return static::_connection()->connection->users;
	}

	public static function push($field, $data)
	{
		$user = static::getUser();
		return static::collection()->update(array(
			'_id' => $user->_id),
			 array('$pushAll' => array($field => $data))
		);
	}

	public static function getUser($fields = null,$sessionKey = 'userLogin') {
		$user = Session::read($sessionKey);
		return User::find('first', array(
			'conditions' => array(
				'_id' => $user['_id']),
			'fields' => $fields
		));
	}
	/**
	 * Cleans the user document.
	 */
	public static function clean($data) {
		if ($data) {
			$data->legacy = 0;
			unset($user->salt);
		}
	}

	/**
	 * The lookup method takes the email address or id to search and finds
	 * the matching user.
	 *
	 * @param string $searchby - email or id
	 */
	public static function lookup($searchBy) {
		$user = null;

		 Validator::add('mongoId', function($value) {
			return (strlen($value) >=10) ? true : false;
		});
		if (Validator::isEmail($searchBy)) {
		    $searchBy = strtolower($searchBy);
			 $condition = array('email' => $searchBy);
		} else if (Validator::isMongoId($searchBy)) {
			$condition = array('_id' => new MongoId($searchBy));
		} else {
			$condition = array('_id' => $searchBy);
		}
		$result = static::collection()->findOne($condition);
		if ($result) {
			$user = User::create($result);
		}
		return $user;
	}

	public static function log($ipaddress) {
		$user = static::getUser();
		if (!$user) {
			return;
		}
		++$user->logincounter;
		$user->lastip = $ipaddress;
		$user->lastlogin = new MongoDate();
		return $user->save(null,array('validate' => false));
	}

	public static function applyCredit($user_id, $credit) {
		$user = User::find('first', array(
			'conditions' => array(
				'_id' => $user_id
		)));
		$user->total_credit = $user->total_credit + $credit;
		return $user->save(null,array('validate' => false));
	}

	/**
	 * The method is in charge of setting up the site cookie if it does not
	 * already exists
	 * @param (boolean) $rememberme
	 * @see app/controllers/UserController::login()
	 */
	public static function rememberMeWrite($rememberme) {
	    if( (boolean)$rememberme && Session::check('cookieCrumb', array('name' => 'cookie'))) {
            $rememberHash = static::generateToken() . static::randomString();
            $cookie = Session::read('cookieCrumb', array('name' => 'cookie'));

            $userInfo = Session::read('userLogin');
            $user = static::collection();
            $info = $user->findOne(array(
                    'email' => $userInfo['email']
                    ),array('autologinHash' => 1, '_id' => -1));
            if ($info && array_key_exists('autologinHash', $info)) {
            	$rememberHash = $info['autologinHash'];
            } else {
				$user->update(array(
						'email' => $userInfo['email']
						), array(
							'$set' => array(
								'autologinHash' => $rememberHash
						)));
            }
            $cookie['user_id'] = $userInfo['_id'];
            $cookie['autoLoginHash'] = $rememberHash;
            Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
        }
	}
	/**
	 * The method is in charge of setting up the site cookie if it does not
	 * already exists
	 * @see app/controllers/BaseController::_init()
	 */
	public static function setupCookie() {
		$cookieInfo = null;
		$urlredirect = ((array_key_exists('redirect',$_REQUEST))) ? $_REQUEST['redirect'] : null ;
		if ( preg_match('(#|/a/|/login|/register|/join/|/invitation/#)', $_SERVER['REQUEST_URI']) ) {
			if(!Session::check('cookieCrumb', array('name' => 'cookie')) ) {
				$cookieInfo = array(
						'user_id' => Session::read('_id'),
						'landing_url' => $_SERVER['REQUEST_URI'],
						'entryTime' => strtotime('now'),
						'redirect' => $urlredirect
					);
			   Session::write('cookieCrumb', $cookieInfo ,array('name' => 'cookie'));
			}else{
				$cookieInfo = Session::read('cookieCrumb', array('name' => 'cookie'));
				$cookieInfo['redirect'] = $urlredirect;
				Session::write('cookieCrumb', $cookieInfo ,array('name' => 'cookie'));
			}
		}
	}

	/**
	 * method to validate some contact us form fields
	 * In case of no error return boolean true
	 * Otherwise return errors array
	 */
	public static function validateContactUs(array $data){
		$rules = array(
		    'firstname' => array('notEmpty', 'message' => 'Please enter a First Name'),
		    'lastname' => array('notEmpty', 'message' => 'Please enter a Last Name'),
			'telephone' => array('notEmpty', 'message' => 'Please enter a Telephone number'),
			'message' => array('notEmpty', 'message' => 'Please type your message')
		);
		$result = array();
		$result = Validator::check($data, $rules);

		if (is_array($result) && count($result)==0){
			return true;
		} else {
			return $result;
		}
	}

	/**
	* The method retrieves a user's invitation_code by _id
	* @param $_id : the user's id
	* @return $invite_code user's invitation code or null
	* @see app/controllers/AffiliatesController::registration()
	**/
	public static function retrieveInvitationCode($_id = null) {
	    if (is_null($_id)) return null;
	    Validator::add('mongoId', function($value) {
			return (strlen($value) >=10) ? true : false;
		});
		if (Validator::isMongoId($_id)) {
			$condition = array('_id' => new MongoId($_id));
		} else {
			$condition = array('_id' => $_id);
		}
	    $invite_code = User::find('first', array('conditions' => $condition,
	        'fields' => array('invitation_codes' => 1)
	    ));
	   $invite_code = $invite_code['invitation_codes'][0];
	    return $invite_code;
	}
	/**
	* Check if we have access to user's facebook info
	* @param (string) $fbid
	* @return array of fb information otherwise null
	**/
	public static function fbAccessCheck($fbid) {
        try {
            $accessToken = FacebookProxy::getAccessToken();
            $facebk_id = $fbid;
            $authCheck = FacebookProxy::api("/$facebk_id?access_token=$accessToken");
            $connected = (!empty($authCheck['email'])) ? true : false;
        } catch (\Exception $e) {
            $authCheck = array();
            $connected = false;
        }
        return $authCheck;
	}

	/**
	* Check if fb account is already linked to user's Totsy account
	* @param $fbid user facebook id
	* @return boolean
	**/
	public static function fbTotsyLinkCheck($fbid) {
	    $connected = true;
	    try {
            $check = User::find('first', array(
                'conditions' => array(
                        'facebook_info.id' => $fbid
            )));
        } catch (\Exception $e) {
            $connected = false;
        }
        return $connected;
	}

	/**
	* Links up a user's totsy account with their fb account
	* @param $searchby user Totsy id or email
	* @param $fbinfo user fb info
	* @return boolean
	* @see app/models/User::lookup()
	**/
	public static function fbTotsyLinkUp($searchby = null, $fbinfo) {
	    $user = static::lookup($searchby);
	    $success = false;
	    if ($user && $fbinfo) {
	        $user->facebook_info = $fbinfo;
	        $success = $user->save(null, array('validate' => false));
		}

		return $success;
	}

	public static function cleanSession() {
		if(Session::check('userSavings')) {
			Session::delete('userSavings');
		}
		if(Session::check('promocode')) {
			Session::delete('promocode');
		}
		if(Session::check('credit')) {
			Session::delete('credit');
		}
		if(Session::check('services')) {
			Session::delete('services');
		}
		if(Session::check('cc_infos')) {
			Session::delete('cc_infos');
		}
		if(Session::check('cc_error')) {
			Session::delete('cc_error');
		}
		if(Session::check('shipping')) {
			Session::delete('shipping');
		}
		if(Session::check('billing')) {
			Session::delete('billing');
		}
		if(Session::check('service_available')) {
			Session::delete('service_available');
		}
	}
	
	/**
	* Check if User has Already a CyberSource Profile link with his credit card
	* If yes, return the cyberSourceProfileId
	* If no, return null
	**/
	public static function hasCyberSourceProfile($cyberSourceProfiles, $creditCard) {
		$cyberSourceProfileDetected = null;
		foreach($cyberSourceProfiles as $cyberSourceProfile) {
			if(!is_string($cyberSourceProfile)) {
				if ($cyberSourceProfile[creditCard][number] == substr($creditCard[number],-4)
					&& $cyberSourceProfile[creditCard][month] == $creditCard[month]
					&& $cyberSourceProfile[creditCard][year] == $creditCard[year]) {
						$cyberSourceProfileDetected = $cyberSourceProfile;
				}
			}
		}
		return $cyberSourceProfileDetected;
	}
}

?>
