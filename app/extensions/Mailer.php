<?php

namespace app\extensions;

use Swift_Message;
use Swift_SmtpTransport;
use Swift_Mailer;
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
		$config = Environment::get('development');

		// get a transport
		$transport = Swift_SmtpTransport::newInstance()
			->setHost($config['mail']['host']) 
			->setUsername($config['mail']['username'])
			->setPassword($config['mail']['password'])
			->setPort($config['mail']['port'])
			;
		$data['domain'] = "http://".$config['mail']['domain'];
		// make a message
		$message = Swift_Message::newInstance($transport);
		$message->setFrom(array('noreply@totsy.com' => 'Totsy'));
		$message->setSubject($subject);
		$message->setTo(array($to['email'] => $to['name']));
		$message->setContentType("text/html");
		$message->setBody(static::_view()->render('all', $data, compact('template')));
		//Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
		
		//Send the message
		$result = $mailer->send($message);

	}
}

?>
