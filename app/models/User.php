<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use \lithium\storage\session\adapter\Cookie;
use \MongoDate;
use \MongoId;
use \lithium\util\Validator;

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
			array('email', 'message' => 'Email is not valid'),
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
	 * The lookup method takes the email address to search and finds
	 * the user by that address.
	 *
	 * @param string $email
	 */
	public static function lookup($email) {
		$user = null;
		$email = strtolower($email);
		$result = static::collection()->findOne(array('email' => $email));
		if ($result) {
			$user = User::create($result);
		}
		return $user;
	}

	public static function log($ipaddress) {
		$user = static::getUser();
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
	public static function setupCookie() {
		$cookieInfo = null;
		$urlredirect = ((array_key_exists('redirect',$_REQUEST))) ? $_REQUEST['redirect'] : null ;
		if ( preg_match('(/|/a/|/login|/register|/join/|/invitation/)', $_SERVER['REQUEST_URI']) ) {
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
				$cookieInfo['entryTime'] = strtotime('now');
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
		    'firstname' => array('notEmpty' => 'Please enter a First Name'),
		    'lastname' => array('notEmpty', 'message' => 'Please enter a Last Name'),
			'telephone' => array('notEmpty', 'message' => 'Please enter a Telephone number')
		);
		$result = array();
		$result = Validator::check($data, $rules);
		
		if (is_array($result) && count($result)==0){
			return true;
		} else { 
			foreach ($result as $k=>$r){
				if (is_array($r)){
					foreach ($r as $a=>$s){
						if ($s == 0) unset($result[$k][$a]);
					}
					if (is_array($r) && count($r)==0){
						unset($result[$k]);
					}
				} else if ($r == 0) {
					unset($result[$k]);
				}
			}
			if ($is_array($result) && count($result) == 0){
				return true;
			} else {
				return $result;
			} 
		}
	}
}


?>