<?php

namespace app\models;

/*
 * 2011-06-21 Upadte notes
 *  - Moved errorCodes Method to ApiHelper::errorCodes();
 *  - Removed unused methods as validate_auth_key item atc.
 */

/*
 * 2011-06-16 Upadte notes
 *  - Api::errorCodes(): array_key_exists firrst parameter must be integer or string fixed
 *  - Api::doAuthorization() & Api::doTokenization(): 
 *  	error handeling fixed
 *  - Api::doAuthorization(): calls proper Api::validateAuthToken()
 *  - Api::validateAuthToken(): error handling fixed
 *  - Api::validate_Sig(): removed tmp_token checker
 *  - Api::validate_Sig(): auth_key replced with auth_token
 *  - Api::validate_Sig(): private_key replaced with private_token
 *  - Api::validate_Sig() & Api::validateAuthToken(): 
 *  	Api::findAuthKey() replaced with private_token
 *  - Api::validateAuthToken(): return value fixed
 */

/*
 * approximate schema for API.credentials
 * {{{
 * 		"_id" : ObjectId("4d6dda33538926843a0026ad"),
 * 		"user_id" : ObjectId("1d60daff5a892d843a0a96fd"),
 * 		"auth_key": ,
 * 		"private_key": ,
 * 		"tmp_token": ,
 * 		"token": ,
 * 		"tmp_token_expires": , // seconds
 * 		"token_expires": , // seconds (10 min)
 * 		"last_active" : ,
 * 		"created":
 * }}}
 * 
 * array (
"_id" => new MongoId(),
"user_id" => new MongoId(),
"auth_key" => md5(time().uniqueid(mt_rand())),
"private_token" => hash('sha256',mktime().uniqueid(mt_rand())),
"tmp_token" => substr(md5(mktime()),0,6).".".substr(strrev(md5(uniqueid())),0,6),
"tmp_token_expires" => new MongoDate(strtotime("+30 seconds")),
"token_expires" => new MongoDate(strtotime("+600 seconds")),
"last_active" => new MongoDate(time()),
"created" => new MongoDate(time())
)
 * 
 * 
 */

//use admin\models\User;

use app\extensions\helper\ApiHelper;

use lithium\util\Validator;
use MongoId;
use MongoDate;
use User;

class Api extends \lithium\data\Model {
	
	protected static $_tmp_token_expires = 30; // in seconds
	protected static $_token_expires = 15; // in minutes
	protected static $_securePorts = array(443,81);
	protected static $_isSecure = false;
	protected static $_requestMethod = null;
	
	public static function init(&$request=null){
		if (!is_null($request)){
			static::setProtocol($request);
			static::setRequestMethod($request);
		}
	}
	
	public static function collection() {
		return static::_connection()->connection->{"api.credentials"};
	}

	public static function findAuthToken ($key) {
		return static::collection()->findOne(array ( 'auth_token' => $key ) );
	}
	
	public static function findToken ($key) {
		return static::collection()->findOne(array ( 'token' => $key ) );
	}
	
	/*
	 *  !!! IMPORTANT !!!
	 *  at virtual host nginx config
	 *  for ssl virtual host we need to add
	 *  the following line needs to be put in the .Location 
	 *  of the nginx config handling php
	 *  "fastcgi_param HTTPS on;"
	 * 
	 */
	public static function setProtocol (&$request){

		if (in_array($request->env('SERVER_PORT'), static::$_securePorts) && ($request->env('HTTPS') == true || $request->env('HTTPS') == 'on')){
			static::$_isSecure = true;
		} else {
			static::$_isSecure = false;
		}
	}
	
	public static function isSecure(){
		return static::$_isSecure;
	}
	
	public static function setRequestMethod (&$request){
		static::$_requestMethod = strtoupper( $request->env('REQUEST_METHOD') );
	}

	public static function isGet(){
		if (static::$_requestMethod == 'GET'){ return true; }
		else { return false; }
	}

	public static function isPost(){
		if (static::$_requestMethod == 'POST'){ return true; }
		else { return false; }
	}
	
	public static function isDelete(){
		if (static::$_requestMethod == 'DELETE'){ return true; }
		else { return false; }
	}
	
	public static function authorizeTokenize ($query){
		/*if (!is_array($query)) { return ApiHelper::errorCodes(407); }		
		if (array_key_exists('auth_token', $query)){
			return self::doAuthorization($query);
		} else if (array_key_exists('token', $query)) {
			return self::doTokenization($query);
		} else {
			return ApiHelper::errorCodes(198);
		}*/
	}
	
	private static function doAuthorization($query){
		if (($error=static::validateAuth($query))!==true) { return $error; }
		$for = self::findAuthToken($query['auth_token']);
		if (!is_array($for) || (is_array($for) && !array_key_exists('auth_token', $for))){
			return ApiHelper::errorCodes(199);
		}
		$expires = $for['last_active']->sec * 60 * self::$_token_expires ;
		if ($expires> time()){
			return self::generateToken($for);
		} else {
			return ApiHelper::errorCodes(198);
		}
	}

	private static function doTokenization($query){
		if (($error=static::validateToken($query))!==true) { return $error; }
		$for = self::findToken($query['token']);
		if (!is_array($for) || (is_array($for) && !array_key_exists('auth_token', $for))){
			return ApiHelper::errorCodes(196);
		}
		return  self::generateToken($for);
	}
	
	public static function generateToken ($user){
		
		if (!is_array($user)) { return ApiHelper::errorCodes(500); }
		$token_expires = $user['last_active']->sec  + 60 * self::$_token_expires;
		if ( $token_expires > time()) {
			$user['last_active'] = new MongoDate();
		} else {
			$user['token'] = md5(time().uniqid(mt_rand()).'+'.$token_expires);
			$user['last_active'] = new MongoDate();
		}
		static::collection()->save($user);
		return $user['token'];
	}
	
	public static function changePassword (&$token){
		$api_user = $this->connection()->findOne(array('token' => $token));
		User::update('');
	} 

	public static function validateToken($query){
		if (($code=static::validate_Token($query))!==true) { return array('error' => ApiHelper::errorCodes($code)); }
		if (static::$_isSecure === false){
			if (($code=static::validate_Time($query))!==true) { return array('error' => ApiHelper::errorCodes($code)); }
			if (($code=static::validate_Sig($query))!==true) { return array('error' => ApiHelper::errorCodes($code)); }
		}			
		return true;
	}
		
	public static function validateAuth($query){		
		if (($code=static::validate_AuthToken($query))!==true) { return ApiHelper::errorCodes($code); }
		if (static::$_isSecure === false){
			if (($code=static::validate_Time($query))!==true) { return ApiHelper::errorCodes($code); }
			if (($code=static::validate_Sig($query))!==true) { return ApiHelper::errorCodes($code); }
		}
		return true;
	}
	
	protected static function validate_AuthToken ($value){
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('auth_token', $value)) { return 197; }
		// in case there is more than 40 charctres alowed
		if ( strlen(trim($value['auth_token'])) != 40) { return 6; }
		// replace all non hash caractres to ""
		$auth_token = preg_replace("/[^a-f0-9]+/", "", strtolower($value['auth_token']));
		// in case replaced carartecs 
		if ( strlen($auth_token) != 40) { return 6; }
		return true;
	}
	
	protected static function validate_Token ($value){
		
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('token', $value)) { return 12; }
		// in case there is more than 32 charctres alowed
		if ( strlen(trim($value['token'])) != 32) { return 13; }
		// replace all non hash caractres to ""
		$token = preg_replace("/[^a-f0-9]+/", "", strtolower($value['token']));
		// in case replaced carartecs 
		if ( strlen($token) != 32) { return 13; }
		
		return true; 
	}
	
	protected static function validate_Time($value){

		if (!is_array($value)) { return 2; }
		if (!array_key_exists('time', $value)) { return 4; }
		
		$time = $value['time'];
		if ( !is_numeric($time) ) return 7;
		if (strlen($time) < 10) return 7; 
		if ($time<strtotime('-10 minutes') || $time>strtotime('+10 minutes')) return 7;
		
		return true;

	}
	
	protected static function validate_Sig($value) {
		
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('sig', $value)) { return 5; }
		
		if ( strlen(trim($value['sig'])) != 32) { return 85; }
		$sig = preg_replace("/[^a-f0-9]+/", "", strtolower(trim($value['sig'])));
		if ( strlen($sig) != 32) { return 84; }		
		
		$data = $value;
		unset($data['sig']);
		ksort($data);
		
		$creds['private_token'] = null;
		if (array_key_exists('auth_token', $data)){
			$creds = self::findAuthToken($data['auth_token']);
			if (!is_array($creds) || (is_array($creds) && !array_key_exists('auth_token', $creds))) return 81;
		} else if (array_key_exists('token', $data)){
			$creds = self::findToken($data['token']);
			if (!is_array($creds) || (is_array($creds) && !array_key_exists('token', $creds))) return 83;
		}

		$sig_confirm = md5($creds['private_token'].implode("", $data));
		if ($sig != $sig_confirm) { return 86; }			
		
		return true;
	}
	
}
?>