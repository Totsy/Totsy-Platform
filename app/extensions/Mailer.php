<?php

namespace app\extensions;

use Sailthru;
use lithium\template\View;
use lithium\core\Environment;
use lithium\action\Request;

class Mailer {
	
	public static function send($template, $email, $vars = array(), $options = array(), $schedule_time = null) {
		Sailthru::send($template, $email, $vars, $options, $schedule_time);
	}
	
	public static function addToMailingList ($email,array $args = array()){
		Sailthru::setEmail(
             $email,  
             $args,
             array('registered' => 1)
        );
	}
	
	public static function purchase($email,array $items = array(), array $args = array()){	
		$data = array(
            'email' => $email,
            'items' => $items
        );

		if (!isset($args['incomplete'])) $data['incomplete'] = null;
        
		if (isset($_COOKIE['sailthru_bid'])) $data['message_id'] = $_COOKIE['sailthru_bid'];
        else if (isset($_COOKIE['sailthru_hid'])) $data['message_id'] = $_COOKIE['sailthru_hid'];
        else if (isset($args['incomplete'])) $data['message_id'] = $args['incomplete'];
        
		return Sailthru::apiPost('purchase',$data);
	}
	
}

?>
