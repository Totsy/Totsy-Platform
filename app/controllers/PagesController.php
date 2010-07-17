<?php

namespace app\controllers;

use app\models\User;
use app\models\Menu;
use lithium\storage\Session;
use app\controllers\BaseController;

class PagesController extends BaseController {

	public function view() {
		$path = func_get_args();
		if (empty($path)) {
			$path = array('home');
		}
		$this->render(join('/', $path));
	}

}

?>