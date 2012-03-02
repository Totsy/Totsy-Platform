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
	
	public static function retrieveCard($profileID) {
		$payments = static::$_classes['payments'];
		
		$cybersource = new CyberSource($payments::config('default'));
		
		$creditcard = array();
		
		if($profileID) {
			$profile = $cybersource->profile($profileID);
			$profile = CreditCard::parseObject($profile);

			if ($profile[variables][decision] != 'REJECT') {
				$array_obj = $profile;							
				$array_obj = $array_obj[variables];			
															 
				$creditcard[profileId] = $profileId;
	    		switch ($array_obj[cardType]) {
	    			case '001': $creditcard[type] = 'Visa'; break;
	    			case '002': $creditcard[type] = 'Mastercard'; break;
	    			case '003': $creditcard[type] = 'American Express'; break;
	    			case '004': $creditcard[type] = 'Discover'; break;
	    			case '005': $creditcard[type] = 'Mastercard'; break;
	    			case '007': $creditcard[type] = 'JCB'; break;
	    			case '014': $creditcard[type] = 'Enroute'; break;
	    			case '024': $creditcard[type] = 'Maestro'; break;
	    			case '033': $creditcard[type] = 'Electron'; break;    			    			    			    			   	    
	    		}
	    		
	    		$creditcard[number] = $array_obj[cardAccountNumber];
	    		$creditcard[month] = $array_obj[cardExpirationMonth];
	    		$creditcard[year] = $array_obj[cardExpirationYear];
	    		
	    		$creditcard[firstname] = $array_obj[firstName];
	    		$creditcard[lastname] = $array_obj[lastName];
	
				$creditcard[address] = $array_obj[street1];
				$creditcard[address2] = $array_obj[street2];
				$creditcard[city] = $array_obj[city];
				$creditcard[state] = $array_obj[state];
				$creditcard[zip] = $array_obj[postalCode];
			}
		}
		return $creditcard;
	}
	
	public static function add($vars) {
		$payments = static::$_classes['payments'];
		$usersCollection = User::Collection();
		$userInfos = User::lookup($vars['user']['_id']);

		$creditCard = $vars['creditCard'];

		#If credit card added manually by User, only retrieve manual credit card added
		if($vars['savedByUser']) {
			$save = true;
		} else {
			$save = false;
		}
		#If Transaction Get Order ID
		if($vars['order_id']) {
			$order_id = $vars['order_id'];
		} else {
			$order_id = null;
		}
		
		$cyberSourceProfileDuplicate = User::hasCyberSourceProfile($userInfos['cyberSourceProfiles'], $creditCard);	
		if ($cyberSourceProfileDuplicate) {
			#Check if SavedByUser
			if($save) {
				foreach($userInfos['cyberSourceProfiles'] as $key => $cyberSourceProfile) {
					if($cyberSourceProfile['profileID'] == $cyberSourceProfileDuplicate['profileID'] && !$cyberSourceProfile['savedByUser']) {
						$usersCollection->update(array('_id' => $userInfos['_id']), array('$set' => array('cyberSourceProfiles.'.$key.'.savedByUser' => true)));
						return "success"; //returned duplicate before but should tell the user the card has been saved instead of saying it's a duplicate
					}
				}
			}
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
			#Check if a soft Auth has been made and use it to save CyberSourceProfile
			if($vars['auth']) {
				$result = $payments::profile('default', $vars['auth'], array('orderID' => $order_id));
			} else {		
				#Create A User Profile with CC Infos Through Auth.Net
				$customer = $payments::create('default', 'customer', array(
					'id' => $order_id,
					'firstName' => $userInfos['firstname'],
					'lastName' => $userInfos['lastname'],
					'email' => $userInfos['email'],
					'billing' => $payments::create('default', 'address', $address),
					'payment' => $payments::create('default', 'creditCard', $creditCard)
				));
				$result = $customer->save();
			}
			#If Profile Well Saved, Record in DB all the information
			if($result->success) {
				$newCyberSourceProfile['profileID'] = $result->response->paySubscriptionCreateReply->subscriptionID;
				$newCyberSourceProfile['creditCard']['number'] = substr($creditCard['number'], -4);
				$newCyberSourceProfile['creditCard']['month'] = $creditCard['month'];
				$newCyberSourceProfile['creditCard']['year'] = $creditCard['year'];
				$newCyberSourceProfile['creditCard']['type'] = $creditCard['type'];
				$newCyberSourceProfile['billing'] = $address;
				if($vars['savedByUser']) {
					$newCyberSourceProfile['savedByUser'] = true;
				}
				$update = $usersCollection->update(
					array('_id' => $userInfos['_id']),
					array('$push' => array('cyberSourceProfiles' => $newCyberSourceProfile)), array( 'upsert' => true)
				);
				return $newCyberSourceProfile;
			} else { //return an error
				return $result;
			} //end of error / success
		} //end of duplicate check bracket
	}
	
	public static function remove_creditcard($user_id, $profileID) {
		$usersCollection = User::Collection();
		$user = User::lookup($user_id);
		$update = $usersCollection->update(
			array('_id' => $user['_id']),
			array('$pull' => array('cyberSourceProfiles' => array('profileID' => $profileID))), array( 'upsert' => true)
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
	
	/**
	 * Decrypt credit card informations stored in the Session
	 */
	public static function decrypt($user_id) {
		$cc_encrypt = Session::read('cc_infos');

		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
 		$iv =  base64_decode(Session::read('vi'));
		foreach	($cc_encrypt as $k => $cc_info) {
			$crypt_info = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($user_id . $k), base64_decode($cc_info), MCRYPT_MODE_CFB, $iv);
			$card[$k] = $crypt_info;
		}
		return $card;
	}
	
	/**
	* Encrypt all credits card informations with MCRYPT and store it in the Session
	*/
	public static function encrypt($cc_infos, $user_id,$save_iv_in_session = false) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		if ($save_iv_in_session == true) {
			Session::write('vi',base64_encode($iv));
		}
		foreach	($cc_infos as $k => $cc_info) {
			$crypt_info = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($user_id . $k), $cc_info, MCRYPT_MODE_CFB, $iv);
			$cc_encrypt[$k] = base64_encode($crypt_info);
		}
		return $cc_encrypt;
	}
}