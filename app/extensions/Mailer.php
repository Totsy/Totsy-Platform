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

		if (!empty($args['incomplete'])) $data['incomplete'] = $args['incomplete'];
		
		if (isset($_COOKIE['sailthru_bid'])) $data['message_id'] = $_COOKIE['sailthru_bid'];
        
		return Sailthru::apiPost('purchase',$data);
	}
}

?>
