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
		$config = Environment::get('test');

		// get a transport
		$transport = Swift_SmtpTransport::newInstance()
			->setHost($config['mail']['host']) 
			->setUsername($config['mail']['username'])
			->setPassword($config['mail']['password'])
			->setPort($config['mail']['port'])
			;
			
		
		// make a message
		// $message = Swift_Message::newInstance($transport);
		// $message->setFrom(array('info@totsy.com' => 'Totsy'));
		// $message->setSubject('testing');
		// $message->setTo(array("{$to['name']} <{$to['email']}>"));
		// $message->setBody(static::_view()->render('all', $data, compact('template')));
		
		//Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);

		//Create a message
		$message = Swift_Message::newInstance('Wonderful Subject')
		  ->setFrom(array('noreply@totsy.com'))
		  ->setTo(array('f.h.agard@lightcube.us'))
		  ->setBody('Here is the message itself')
		  ;
		
		//Send the message
		$result = $mailer->send($message);

	}
}

?>