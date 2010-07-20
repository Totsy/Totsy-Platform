<?php

namespace app\extensions;

use Swift_Message;
use lithium\template\View;
use lithium\core\Environment;
use lithium\action\Request;

class Mailer {

	protected static $_view;

	protected static function _view() {
		if (static::$_view) {
			return static::$_view;
		}
		return static::$_view = new View(array(
			'paths' => array(
				'template' => '{:library}/views/email/{:template}.html.php',
				'layout'   => '{:library}/views/email/layout.html.php',
			),
			'request' => new Request()
		));
	}

	public static function send($template, $subject, array $to, array $data) {
		$config = Environment::get('mail');
		// get a transport
		$transport = Swift_SmtpTransport::newInstance($config->mail->host, $config->mail->port)
			->setUsername($config->mail->username)
			->setPassword($config->mail->password)
			;
		// make a message
		$message = Swift_Message::newInstance();
		$message->setFrom();
		$message->setSubject();
		$message->setTo("{$to['name']} <{$to['email']}>");
		$message->setBody(static::_view()->render('all', $data, compact('template')));
	}
}

?>