<?php

namespace app\extensions;

use Sailthru;
use lithium\template\View;
use lithium\core\Environment;
use lithium\action\Request;

class Mailer {

	public static function send($template, $email, $vars = array(), $options = array(), $schedule_time = null) {
		// Remove Sailthru until it can be done asynchronously
		//Let transactional email go
		Sailthru::send($template, $email, $vars, $options, $schedule_time);
	}

	public static function addToMailingList ($email,array $args = array(), $list=array("registered"=>1)){
		Sailthru::setEmail(
             $email,
             $args,
             $list
        );
	}

	public static function purchase($email,array $items = array(), array $args = array()){
		$data = array(
            'email' => $email,
            'items' => $items
        );

		if (!empty($args['incomplete'])) $data['incomplete'] = $args['incomplete'];

		if (isset($_COOKIE['sailthru_bid'])) $data['message_id'] = $_COOKIE['sailthru_bid'];

		return Sailthru::apiPost('purchase',$data);
	}

	//This function takes an email (subscribed user) from our system and posts it to Unsubcentral's API under the list subscribed
	public static function addToSuppressionList ($email) {
		$fields_string = "";

		//116 - Registered Users List
		$url = 'https://login8.unsubcentral.com/uc/address_upload.pl?';
		$fields = array(
								'login'=>'TotsyAPI',
								'password'=>'D:hXeM;i',
								'listID'=>'116',
								'md5'=>'false',
								'suppressed_text'=>urlencode($email)
						);

		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&amp;');

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER,0);		
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
		$result = curl_exec($ch);
		curl_close($ch);
	}
}

?>
