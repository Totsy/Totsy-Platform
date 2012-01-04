<?php

namespace app\models;

use MongoId;
use MongoDate;
use lithium\storage\Session;
use app\models\User;
use app\models\Base;
use li3_payments\extensions\adapter\payment\CyberSource;
use li3_payments\extensions\adapter\account\Customer;

class CreditCard extends \lithium\data\Model {

	protected static $_classes = array(
		'tax' => 'app\extensions\AvaTax',
		'payments' => 'li3_payments\payments\Processor'
	);

	protected $_dates = array(
		'now' => 0
	);

	public $validates = array(
		'number' => array(
			'notEmpty', 'required' => false, 'message' => 'Please add a credit card number'
		),
		'year' => array(
			'notEmpty', 'required' => true, 'message' => 'Please select the expiration year'
		),
		'month' => array(
			'notEmpty', 'required' => true, 'message' => 'Please select the expiration month'
		),
		'code' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add the security code'
		)
		/*,
		'firstname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a first name'
		),
		'lastname' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a last name'
		),
		'address' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add an address'
		),
		'telephone' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a telephone number'
		),
		'city' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a city'
		),
		'state' => array(
			'state', 'required' => true, 'message' => 'Please select a state or province'
		),
		'zip' => array(
			'notEmpty', 'required' => true, 'message' => 'Please add a zip code'
		)
		*/
	);

	public static function __init() {
		parent::__init();
		$validator = static::$_classes['validator'];
		$validator::add('state', '[A-Z]{2}', array('contains' => false));
	} 
	
	public static function retrieve_all_cards($user_id, $saved) {
		$payments = static::$_classes['payments'];
		$usersCollection = User::Collection();
		$userInfos = $usersCollection->findOne(array('_id' => new MongoId($user_id)));
		
		$cybersource = new CyberSource($payments::config('default'));
		
		$creditcard = array();
		$i=0;
		
		if(!$saved) {
			array_reverse($userInfos['cyberSourceProfiles']);
			$CyberSourceProfiles = $userInfos['cyberSourceProfiles'];
		} else {
			array_reverse($userInfos['cyberSourceProfilesSavedByUser']);
			$CyberSourceProfiles = $userInfos['cyberSourceProfilesSavedByUser'];
		}	
		
		foreach ($CyberSourceProfiles as $profileId) {
			$profile = $cybersource->profile($profileId);
			$profile = CreditCard::parseObject($profile);

			if ($profile[variables][decision] != 'REJECT') {
				$array_obj = $profile;							
				$array_obj = $array_obj[variables];			
															 
				$creditcard[$i][profileId] = $profileId;
	    		switch ($array_obj[cardType]) {
	    			case '001': $creditcard[$i][type] = 'Visa'; break;
	    			case '002': $creditcard[$i][type] = 'Mastercard'; break;
	    			case '003': $creditcard[$i][type] = 'American Express'; break;
	    			case '004': $creditcard[$i][type] = 'Discover'; break;
	    			case '005': 
	    				//$creditcard[$i][type] = 'Diners Club';
	    				$creditcard[$i][type] = 'Mastercard';
	    			break;
	    			case '007': $creditcard[$i][type] = 'JCB'; break;
	    			case '014': $creditcard[$i][type] = 'Enroute'; break;
	    			case '024': $creditcard[$i][type] = 'Maestro'; break;
	    			case '033': $creditcard[$i][type] = 'Electron'; break;    			    			    			    			   	    
	    		}
	    		
	    		$creditcard[$i][number] = $array_obj[cardAccountNumber];
	    		$creditcard[$i][month] = $array_obj[cardExpirationMonth];
	    		$creditcard[$i][year] = $array_obj[cardExpirationYear];
	    		
	    		$creditcard[$i][firstname] = $array_obj[firstName];
	    		$creditcard[$i][lastname] = $array_obj[lastName];
	
				$creditcard[$i][address] = $array_obj[street1];
				$creditcard[$i][address2] = $array_obj[street2];
				$creditcard[$i][city] = $array_obj[city];
				$creditcard[$i][state] = $array_obj[state];
				$creditcard[$i][zip] = $array_obj[postalCode];
	    		
				$i++;
			}
		}
		
		return $creditcard;
	}
	
	public static function add($vars) {
		$payments = static::$_classes['payments'];
		$usersCollection = User::Collection();
		$userInfos = $usersCollection->findOne(array('_id' => new MongoId($vars['user']['_id'])));

		$creditCard = $vars['creditCard'];
		
		#If credit card added manually by User, only retrieve manual credit card added
		if($vars['savedByUser']) {
			$save = true;
		} else {
			$save = false;
		}
		#Get current credit cards to compare to this card
		$creditcardsSaved = CreditCard::retrieve_all_cards($vars['user']['_id'], $save);
		
		$duplicate = User::hasCyberSourceProfile($creditcardsSaved, $creditCard);	
		
		if ($duplicate) {
			return "duplicate";
		} else {	
			#Create Address Array
			$address = array(
					'firstName' =>  $vars['billingAddr']['firstname'],
					'lastName' => $vars['billingAddr']['lastname'],
					'address' => trim($vars['billingAddr']['address'] . ' ' . $vars['billingAddr']['address2']),
					'city' => $vars['billingAddr']['city'],
					'state' => $vars['billingAddr']['state'],
					'zip' => $vars['billingAddr']['zip'],
					'country' => $vars['billingAddr']['country'] ?: 'US',
					'email' =>  $vars['user']['email'] 
			);

			#Create A User Profile with CC Infos Through Auth.Net
			$customer = $payments::create('default', 'customer', array(
				'firstName' => $userInfos['firstname'],
				'lastName' => $userInfos['lastname'],
				'email' => $userInfos['email'],
				'billing' => $payments::create('default', 'address', $address),
				'payment' => $payments::create('default', 'creditCard', $creditCard)
			));
			$result = $customer->save();
			if($result->success) {
				$profileID = $result->response->paySubscriptionCreateReply->subscriptionID;
				$update = $usersCollection->update(
					array('_id' => new MongoId($vars['user']['_id'])),
					array('$push' => array('cyberSourceProfiles' => $profileID)), array( 'upsert' => true)
				);
				if($vars['savedByUser']) {
					$update = $usersCollection->update(
						array('_id' => new MongoId($vars['user']['_id'])),
						array('$push' => array('cyberSourceProfilesSavedByUser' => $profileID)), array( 'upsert' => true)
					);
				}
				return $profileID;
			} else { //return an error
				return "error";
			} //end of error / success
		} //end of duplicate check bracket
	}
	
	public static function remove_creditcard($user_id, $profileID) {
		$usersCollection = User::Collection();
		$user = User::lookup($user_id);
		$update = $usersCollection->update(
			array('_id' => $user['_id']),
			array('$pull' => array('cyberSourceProfiles' => $profileID)), array( 'upsert' => true)
		);
		$usersCollection->update(
			array('_id' => $user['_id']),
			array('$pull' => array('cyberSourceProfilesSavedByUser' => $profileID)), array( 'upsert' => true)
		);
		return $update;
	}
	
	public static function parseObject($obj, $values=true) {
	    $obj_dump  = print_r($obj, 1);
	    $ret_list = array();
	    $ret_map = array();
	    $ret_name = '';
	    $dump_lines = preg_split('/[\r\n]+/',$obj_dump);
	    $ARR_NAME = 'arr_name';
	    $ARR_LIST = 'arr_list';
	    $arr_index = -1;
	   
	    // get the object type...
	    $matches = array();
	    preg_match('/^\s*(\S+)\s+\bObject\b/i',$obj_dump,$matches);
	    if(isset($matches[1])) { 
	    	$ret_name = $matches[1];
	    }//if
	    foreach($dump_lines as &$line) {
	   
	      $matches = array();
	   
	      //load up var and values...
	      if(preg_match('/^\s*\[\s*(\S+)\s*\]\s+=>\s+(.*)$/', $line, $matches)) {
			if(mb_stripos($matches[2],'array') !== false){
	       
	          $arr_map = array();
	          $arr_map[$ARR_NAME] = $matches[1];
	          $arr_map[$ARR_LIST] = array();
	          $arr_list[++$arr_index] = $arr_map;
	       
	        } else {
	          // save normal variables and arrays differently...
	          if($arr_index >= 0) { 
	            $arr_list[$arr_index][$ARR_LIST][$matches[1]] = $matches[2];
	          } else {
	            $ret_list[$matches[1]] = $matches[2];
	          }//if/else
	        }//if/else	     
	      }else{
	     
	        // save the current array to the return list...
	        if(mb_stripos($line,')') !== false){
	       
	          if($arr_index >= 0){
	           
	            $arr_map = array_pop($arr_list);
	           
	            // if there is more than one array then this array belongs to the earlier array...
	            if($arr_index > 0){
	              $arr_list[($arr_index-1)][$ARR_LIST][$arr_map[$ARR_NAME]] = $arr_map[$ARR_LIST];
	            }else{
	              $ret_list[$arr_map[$ARR_NAME]] = $arr_map[$ARR_LIST];
	            }//if/else
	           
	            $arr_index--;
	           
	          }//if
	       
	        }//if
	     
	      }//if/else
	     
	    }//foreach
	   
	    $ret_map['name'] = $ret_name;
	    $ret_map['variables'] = $ret_list;
	    return $ret_map;
	}//method

}