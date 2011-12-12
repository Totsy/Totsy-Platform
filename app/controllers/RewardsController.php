<?php

namespace app\controllers;

use app\models\User;
use app\models\Credit;
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
	
	//handle flagging enrolled users as 500 friends members in our DB
	public function enrollUser() {
		/* logic for tagging as enrolled here */
		$loginInfo = Session::read('userLogin');	
		$user = User::collection();
			
		$getUser = User::find('first', array(
		    'conditions' => array('fivehundred_friends.active' => 1, '_id' => $loginInfo['_id'])));
		    		    		    
		//if the user isn't found, log them as a 500 friends member	for the 1st time
		if(is_null($getUser)) {			
		    $user->save('first', array('$set' => array('fivehundred_friends.active' => 1, 												'fivehundred_friends.point_count'=>0 ), array('_id' => $loginInfo['_id']))
		    );	
		} 
	}
	
	public function makeAPICall(){
	
		$c = curl_init($apiURL);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		
        //this can be commented out for production
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);   
        
        $result = curl_exec($c);                        
        $data = json_decode($result, true);	
    } 
	
	/* make api calls here - just pas in what kind of data you want to get 
	   can be 
	   show -  
	   events -  
	   badges - 
	   */
	public function getMemberData($apiCall) {	
		$user = Session::read('userLogin');
		$apiURL = "https://loyalty.500friends.com/data/customer/".$apiCall."?";
		
		$params = $this->buildAuthSignature();
		
		//build the rest of this URL
		foreach ($params as $key => $val) {
           $apiURL .= "$key=".urlencode($val);
           if($key!=="sig") {
              $apiURL .= "&";
           }
        }  
        
        $c = curl_init($apiURL);
        
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		
        //this can be commented out for production
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);   
        
        $result = curl_exec($c);                        
        $memberData = json_decode($result, true);
        
        return compact('memberData');
	}
	
	/* make api calls here - just pass in what kind of data you want to get and the paramaters necesary to authenticate 
	   can be
	   		badges - Get all badges.
	   		rewards - Get all rewards
	   		leaderboard - Get all the customers that would be listed on a leaderboard */
	public function getAllMembersData($apiCall, $params) {
		$user = Session::read('userLogin');
		$apiURL = "https://loyalty.500friends.com/data/".$apiCall."?";
		
		//build the rest of this URL
		foreach ($params as $key => $val) {
           $apiURL .= "$key=".urlencode($val);
        }  
        
        $c = curl_init($apiURL);
        
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		
        //this can be commented out for production - because there will an SSL cert in place
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);   
        
        $result = curl_exec($c);                        
        $memberData = json_decode($result, true);
        
        return compact('allMembersData');
	}
	
	public function getTopMembers() {
		$this->getAllMembersData('');
	}
	
	//send token
	public function members() {	
	
		//when loading the members page, enroll them
		$this->enrollUser();	
					
		$user = Session::read('userLogin');	
        $apiURL = "https://loyalty.500friends.com/data/customer/auth_token?";
		
		//for use in the iFrame
		$params = $this->buildAuthSignature();
				
		//build the rest of this URL
		foreach ($params as $key => $val) {
           $apiURL .= "$key=".urlencode($val);
           if($key!=="sig") {
              $apiURL .= "&";
           }
        }  
        
        $c = curl_init($apiURL);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        //this can be commented out for production
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