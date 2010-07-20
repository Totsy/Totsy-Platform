<?php

namespace app\controllers;

use app\models\User;
use app\models\Menu;
use lithium\storage\Session;
use app\controllers\BaseController;

class PagesController extends BaseController {

	/**
	 * Sets up the Menu element for the page
	 */
	protected function _init() {
		parent::_init();
	}
	
	public function view() {
		$path = func_get_args();
		if (empty($path)) {
			$path = array('home');
		}
		$this->render(join('/', $path));
	}

}

?>