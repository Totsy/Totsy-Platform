<?php

namespace app\controllers;

use app\models\User;
use app\models\Menu;
use app\models\Affiliate;
use lithium\security\Auth;
use lithium\storage\Session;
use MongoDate;
use li3_facebook\extension\FacebookProxy;

use app\extensions\Mailer;
use lithium\action\Request;
use \lithium\data\Connections;
use \lithium\util\Validator;

class RewardsController extends BaseController {
	
	//send signature
	public function index() {	
		$params = $this->buildAuthSignature();	
		return compact('params');	
	}
	
	//send token
	public function members() {		
			
		$user = Session::read('userLogin');	
        $apiURL = "https://loyalty.500friends.com/data/customer/auth_token?";
		
		$params = $this->buildAuthSignature();
				
		//build the rest of this URL
		foreach ($params as $key => $val) {
           $apiURL .= "$key=".urlencode($val);
           if($key!=="sig") {
              $apiURL .= "&";
           }
        }  
        
        $c = curl_init($apiURL);
        
        //this can be commented out for production
		
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);   
        
        $result = curl_exec($c);                        
        $parsedResponse = json_decode($result, true);
        
        $authToken = $parsedResponse['data']['auth_token'];
        
		return compact ('authToken','params');
	}
	
	public function buildAuthSignature() {
		//account id, secret key and nonce for API calls with Loyalty Plus program
		$uuId = "k7iyyJWiWIg0DMy";
		$secretKey = "OT1nZIDBRdzbvAXT6cIUH1Dum3zsIbKG";
		$nonce = time().microtime()*1000000;
		$stringToHash = "";
		
		//get current user's email	
		$user = Session::read('userLogin');				
		
		//$params["secret_key"] = $secretKey;
		$params["email"] = $user['email'];
		$params["nonce"] = $nonce;
		$params["uuid"] = $uuId; 
		
		ksort($params);
		
		//make string of all vars
		foreach ($params as $key => $val) {
            $stringToHash .= $key.$val;
        }
                
        //hash that string  
        $params["sig"] = md5($secretKey.$stringToHash);
        
        //return sig and all other fields needed for API calls
		return $params;
	}	
}

?>