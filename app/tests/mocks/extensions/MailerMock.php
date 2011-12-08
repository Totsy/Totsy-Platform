<?php

namespace app\tests\mocks\extensions;

class MailerMock extends \app\extensions\Mailer {
	public static $sent = array();
	public static $mailing_list = array();
	public static $suppression_list = array();

	public static function send($template, $email, $vars = array(), $options = array(), $schedule_time = null) {
		static::$sent[] = func_get_args();
	}

	public static function addToMailingList($email,array $args = array()) {
		static::$mailing_list[] = func_get_args();
	}

	public static function addToSuppressionList($email) {
		static::$suppression_list[] = func_get_args();
	}

	public static function reset() {
		static::$sent = static::$mailing_list = static::$suppression_list = array();
	}
}

?>