<?php

namespace admin\extensions;

use Sailthru;
use lithium\template\View;
use lithium\core\Environment;
use lithium\action\Request;

class Mailer {
	public static function send($template, $email, $vars = array(), $options = array(), $schedule_time = null) {
		Sailthru::send($template, $email, $vars, $options, $schedule_time);
	}

	public static function optOut($email, $vars = array(), $list = array(), $templates = array() , $optout="none") {
		Sailthru::setEmail($email, $vars, $list, $templates);
	}
}

?>