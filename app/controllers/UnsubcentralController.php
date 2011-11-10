<?php

namespace app\controllers;

use app\extensions\Mailer;
use lithium\action\Request;
use \lithium\data\Connections;
use \lithium\util\Validator;

class UnsubcentralController extends  \lithium\action\Controller {

	protected $_method = null;
	protected $_format = null;
	protected $_view = null;
		
	//This function takes an email (unsubscribed user) from Sailthru and posts it to Unsubcentral's API under the list unsubscribed
	public function unsubscribed() {
		$fields_string = "";

		//113 - Opt-out Registered Users
        $params = $_POST;
		
		$email 	= $params['email'];	
		$urlencoded_email = urlencode($email);
		
		//set POST variables - activate a user on the opt out list
		$url = 'https://login8.unsubcentral.com/uc/address_upload.pl?';
		$fields = array(
								'login'=>'TotsyAPI',
								'password'=>'D:hXeM;i',
								'listID'=>'113',
								'md5'=>'false',
								'suppressed_text'=>urlencode($email)
						);
		
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&amp;');
				
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
		$result = curl_exec($ch);
		curl_close($ch);	
		
		//clear the previous variables
		unset($fields);
		unset($fields_string);
		
		$date_today = date("Y-m-d");
		
		$unsubcentral_filename = "/tmp/unsubcentral-".time().".txt";
		$temp_file = file_put_contents($unsubcentral_filename,"{$email}\t{$date_today}\t0\t\n");
		
		//deactivate a user on the registered list (116)
		$url = "https://login8.unsubcentral.com/uc/add_remove_address.pl?";
		$fields = array(
								'login'=>'TotsyAPI',
								'password'=>urlencode('D:hXeM;i'),
								'listID'=>'116',
								'file'=>"@{$unsubcentral_filename}",
								'email_col'=>"0",
								'action_col'=>"2",
								'date_col'=>"1"
						);
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,TRUE);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE); 
		$result = curl_exec($ch);
		curl_close($ch);
		
		//remove the unsubcentral upload file after it is used for upload
		unlink($unsubcentral_filename);
  	}
	
}

?>