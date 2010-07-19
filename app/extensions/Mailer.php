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

	public static function send($template, array $data) {
		die(static::_view()->render('all', $data, compact('template') + array(
		)));
	}
}

?>