<?php

namespace app\models;

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

use lithium\util\Validator;
use MongoId;
use MongoDate;
use User;

class Api extends Base {
	
	protected static $_tmp_token_expires = 30; // in seconds
	protected static $_token_expires = 15; // in minutes
	protected static $_securePort = 443;
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
	
	public static function setProtocol (&$request){
		if ($request->env('SERVER_PORT') == static::$_securePort && $request->env('HTTPS') == true ){
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
		if (!is_array($query)) { return static::errorCodes(407); }		
		if (array_key_exists('auth_token', $query)){
			return self::doAuthorization($query);
		} else if (array_key_exists('token', $query)) {
			return self::doTokenization($query);
		} else {
			return static::errorCodes(198);
		}
	}
	
	private static function doAuthorization($query){
		if (($code=static::validate_AuthToken($query))!==true) { return self::errorCodes($code); }
		$for = self::findAuthToken($query['auth_token']);
		if (!is_array($for) || (is_array($for) && !array_key_exists('auth_token', $for))){
			return static::errorCodes(199);
		}
		$expires = strtotime($for['last_active']) * 60 * self::$_token_expires ;
		if ($expires> time()){
			return self::generateToken($for);
		} else {
			return static::errorCodes(198);
		}
	}

	private static function doTokenization($query){
		if (($code=static::validate_Token($query))!==true) { return self::errorCodes($code); }
		$for = self::findToken($query['token']);
		if (!is_array($for) || (is_array($for) && !array_key_exists('auth_token', $for))){
			return static::errorCodes(199);
		}
		return  self::generateToken($for);
	}
	
	public static function generateToken ($user){
		
		if (!is_array($user)) { return static::errorCodes(500); }
		$token_expires = strtotime( (string) $user['last_active'])  * 60 * self::$_token_expires;
		if ( $token_expires >time()) return $user['token'];
		
		$user['token'] = md5(mktime().uniqid(mt_rand()).'+'.$token_expires);
		$user['last_active'] = new MongoDate();
		static::collection()->save($user);
		return $user['token'];
	}
	
	public static function changePassword (&$token){
		$api_user = $this->connection()->findOne(array('token' => $token));
		User::update('');
	} 

	public static function validateToken($query){
		if (($code=static::validate_Token($query))!==true) { return array('error' => self::errorCodes($code)); }
		if (static::$_isSecure === false){
			if (($code=static::validate_Time($query))!==true) { return array('error' => self::errorCodes($code)); }
			if (($code=static::validate_Sig($query))!==true) { return array('error' => self::errorCodes($code)); }
		}			
		return true;
	}
		
	public static function validateAuth($query){		
		if (($code=static::validate_AuthKey($query))!==true) { return array('error' => self::errorCodes($code)); }
		if (static::$_isSecure === false){
			if (($code=static::validate_Time($query))!==true) { return array('error' => self::errorCodes($code)); }
			if (($code=static::validate_Sig($query))!==true) { return array('error' => self::errorCodes($code)); }
		}
		return self::findAuthKey($query['auth_key']);
	}
	
	public static function errorCodes($status = 500){
		$codes = Array(
			  2 => 'Missing HTTP-Request Parameter',
			  3 => 'Unknown AUTH_KEY',
			  4 => 'Unknown TIME',
			  5 => 'Unknown SIG',
			  6 => 'Invalid AUTH_KEY',
			  7 => 'Paremeter TIME is out of range',
			  8 => 'Invalid SIG',
			  9 => 'Unknown TMP_TOKEN',
			 10 => 'Invalid TMP_TOKEN',
			 11 => 'Expired TMP_TOKEN',
			 12 => 'Unknown TOKEN',
			 13 => 'Invalid TOKEN',
			 14 => 'Expired TOKEN',
			 15 => 'Unknown ITEM_ID',
			 16 => 'Invalid ITEM_ID',			 
			 81 => 'sig 1',
			 82 => 'sig 2',
			 83 => 'sig 3',
			 84 => 'sig 4',
			 85 => 'sig 5',
			 86 => 'sig 6',
			 93 => 'Variable "control" invalid',
			 94 => 'Variable "time" invalid',	 
			 95 => 'Variable "auth_id" invalid',
		 	 96 => 'No "auth_id" variable in the request',	    
			 97 => 'No "time" variable in the request',
			 98 => 'No "control" variable in the request',
			 99 => 'Request-URI Too short',
			100 => 'Continue', 		    
			101 => 'Switching Protocols',
			198 => 'Authorization failed',
			199 => 'Invalid AUTH_TOKEN', 		    
			200 => 'OK', 		    
			201 => 'Created', 		    
			202 => 'Accepted', 		    
			203 => 'Non-Authoritative Information', 		    
			204 => 'No Content', 		    
			205 => 'Reset Content', 		    
			206 => 'Partial Content', 		    
			300 => 'Multiple Choices', 		    
			301 => 'Moved Permanently', 		    
			302 => 'Found', 		    
			303 => 'See Other', 		    
			304 => 'Not Modified', 		    
			305 => 'Use Proxy', 		    
			306 => '(Unused)', 		    
			307 => 'Temporary Redirect', 		    
			400 => 'Bad Request', 		    
			401 => 'Unauthorized', 		    
			402 => 'Payment Required', 		    
			403 => 'Forbidden', 		    
			404 => 'Not Found', 		    
			405 => 'Method Not Found', 		    
			406 => 'Not Acceptable', 		    
			407 => 'Proxy Authentication Required', 		    
			408 => 'Request Timeout', 		    
			409 => 'Conflict', 		    
			410 => 'Gone', 		    
			411 => 'Length Required', 		   
			412 => 'Precondition Failed', 		    
			413 => 'Request Entity Too Large', 		    
			414 => 'Request-URI Too Long', 		    
			415 => 'Unsupported Media Type', 		    
			416 => 'Requested Range Not Satisfiable', 		    
			417 => 'Expectation Failed', 		    
			500 => 'Internal Server Error', 		    
			501 => 'Not Implemented', 		    
			502 => 'Bad Gateway', 		    
			503 => 'Service Unavailable', 		    
			504 => 'Gateway Timeout', 		    
			505 => 'HTTP Version Not Supported' 		
		);  
				
		if (array_key_exists($status, $codes)) { 
			$error = array('code' => $status, 'message' => $codes[$status]);
		} else {
			// in case unknown error code ocurred
			$error = array('code' => 500, 'message' => $codes['500']); 
		}
		
		return compact('error');
	}

	protected function validate_AuthToken ($value){
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('auth_token', $value)) { return 3; }
		// in case there is more than 40 charctres alowed
		if ( strlen(trim($value['auth_token'])) != 40) { return 6; }
		// replace all non hash caractres to ""
		$auth_token = preg_replace("/[^a-f0-9]+/", "", strtolower($value['auth_token']));
		// in case replaced carartecs 
		if ( strlen($auth_token) != 40) { return 6; }
		return true;
	}
	
	protected function validate_AuthKey ($value){
		
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('auth_key', $value)) { return 3; }
		// in case there is more than 32 charctres alowed
		if ( strlen(trim($value['auth_key'])) != 32) { return 6; }
		// replace all non hash caractres to ""
		$auth_key = preg_replace("/[^a-f0-9]+/", "", strtolower($value['auth_key']));
		// in case replaced carartecs 
		if ( strlen($auth_key) != 32) { return 6; }
		
		return true; 
	}

	protected function validate_TmpToken ($value){
		
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('tmp_token', $value)) { return 9; }
		// in case there is more than 32 charctres alowed
		if ( strlen(trim($value['tmp_token'])) != 20) { return 10; }
		// replace all non hash caractres to ""
		$auth_key = preg_replace("/[^a-f0-9]+/", "", strtolower($value['tmp_token']));
		// in case replaced carartecs 
		if ( strlen($auth_key) != 20) { return 10; }
		
		return true; 
	}

	protected function validate_Token ($value){
		
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
	
	protected function validate_Time($value){

		if (!is_array($value)) { return 2; }
		if (!array_key_exists('time', $value)) { return 4; }
		
		$time = $value['time'];
		if ( !is_numeric($time) ) return 7;
		if (strlen($time) < 10) return 7; 
		if ($time<strtotime('-10 minutes') || $time>strtotime('+10 minutes')) return 7;
		
		return true;

	}
	
	protected function validate_Sig($value) {
		
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('sig', $value)) { return 5; }
		
		if ( strlen(trim($value['sig'])) != 32) { return 85; }
		$sig = preg_replace("/[^a-f0-9]+/", "", strtolower(trim($value['sig'])));
		if ( strlen($sig) != 32) { return 84; }		
		
		$data = $value;
		unset($data['sig']);
		ksort($data);
		
		if (array_key_exists('auth_key', $data)){
			$creds = self::findAuthKey($data['auth_key']);
			if (!is_array($creds) || (is_array($creds) && !array_key_exists('auth_key', $creds))) return 81;
		} else if (array_key_exists('tmp_token', $data)){
			$creds = self::findTmpToken($data['tmp_token']);
			if (!is_array($creds) || (is_array($creds) && !array_key_exists('tmp_token', $creds))) return 82;
		} else if (array_key_exists('token', $data)){
			$creds = self::findToken($data['token']);
			if (!is_array($creds) || (is_array($creds) && !array_key_exists('token', $creds))) return 83;
		}

		
		
		$sig_confirm = md5((isset($creds['private_key'])?$creds['private_key']:'').implode("", $data));
		if ($sig != $sig_confirm) { return 86; }			
		
		return true;
	}
	
	protected function validate_ItemId ($value){
		
		if (!is_array($value)) { return 2; }
		if (!array_key_exists('item_id', $value)) { return 15; }
		// in case there is more than 32 charctres alowed
		if ( strlen(trim($value['item_id'])) != 24) { return 16; }
		// replace all non hash caractres to ""
		$item_id = preg_replace("/[^a-f0-9]+/", "", strtolower($value['item_id']));
		// in case replaced carartecs 
		if ( strlen($item_id) != 24) { return 16; }
		
		return true; 
	}
	
	
}
?>