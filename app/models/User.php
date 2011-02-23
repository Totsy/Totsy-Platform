<?php

namespace app\models;

use \lithium\data\Connections;
use \lithium\storage\Session;
use \lithium\storage\session\adapter\Cookie;
use \MongoDate;
use \MongoId;
use \MongoRegex;
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
		'firstname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a first name'
		),
		'lastname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a last name'
		),
			'zip' => array(
				'notEmpty', 'required' => true, 'message' => 'Please add a zip code'
		),
		'email' => array(
			array('email', 'message' => 'Email is not valid'),
			array('notEmpty', 'required' => true, 'message' => 'Please add an email address'),
			array('isUniqueEmail', 'message' => 'This email address is already registered')
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

	public static function getUser($fields = null) {
		$user = Session::read('userLogin');
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
	 * Lookup a user by either their email or username
	 */
	public static function lookup($email) {
		$user = null;
		$email = new MongoRegex("/^$email/i");
		$result = static::collection()->findOne(array(
			'$or' => array(array('username' => $email), array('email' => $email)))
		);
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
            $cookie['user_id'] = $userInfo['_id'];
            $cookie['autoLoginHash'] = $rememberHash;
            Session::write('cookieCrumb', $cookie, array('name' => 'cookie'));
            $user = static::collection();
            $user->update(array(
                    'email' => $userInfo['email']
                    ), array(
                        '$set' => array(
                            'autologinHash' => $rememberHash
                    )));
        }
	}
	public static function setupCookie() {
		$cookieInfo = null;
		$urlredirect = ((array_key_exists('redirect',$_REQUEST))) ? $_REQUEST['redirect'] : null ;
		if ( preg_match('(/|/a/|/login|/register)', $_SERVER['REQUEST_URI']) ) {
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
				$cookieInfo['entryTime'] = strtotime('now');
				$cookieInfo['redirect'] = $urlredirect;
				Session::write('cookieCrumb', $cookieInfo ,array('name' => 'cookie'));
			}
		}
	}
}


?>