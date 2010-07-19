<?php

namespace app\controllers;

use app\extensions\Mailer;

class MailerController extends BaseController {

	public function __invoke() {
		Mailer::send('welcome', array('user' => \app\models\User::first(2960)));
		die('!');
	}
}

?>