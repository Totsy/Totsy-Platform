<?php

namespace admin\extensions;

use Sailthru_Util;
use Sailthru_Client;
use Sailthru_Client_Exception;
use lithium\core\Environment;
use lithium\action\Request;
use lithium\analysis\Logger;

class SailThru {

	/**
	 * -----------------------------------------------------------
	 * ----------------CONFIGURATION VARIABLES--------------------
	 */
	protected static $api_key = '568106ff64d98574392dba282bc3267f';
	protected static $secret = '288e514c962cf8adcd82ff01938b861f';
	/**
	 * -----------------------------------------------------------
	 */
	protected static function buildClient() {
		$sailthru = new Sailthru_Client(static::$api_key, static::$secret);
		return $sailthru;
	}
	
	public static function send($template, $email, $vars = array(), $options = array(), $schedule_time = null) {
		$client = static::buildClient();
		$result = $client->send($template, $email);
		if(array_key_exists('error', $result)) {
			Logger::info('SAILTHRU ERROR: '.$result['errormsg']);
		};
	}
}