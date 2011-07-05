<?php

namespace admin\extensions;

use Sailthru;
use lithium\template\View;
use lithium\core\Environment;
use lithium\action\Request;

class Mailer {
	public static function send($template, $email, $vars = array(), $options = array(), $schedule_time = null) {
		Sailthru::send($template, $email);
	}
}

?>