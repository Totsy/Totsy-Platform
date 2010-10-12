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
		if ($path[0] == 'blog') {
			$this->redirect('http://totsyblog.blogspot.com', array('target' => '_blank'));
		}elseif ($path[0] == 'btrendie' ){
			$this->_render['layout'] = 'blank';
		}
		$allowed = array('terms', 'faq', 'contact', 'privacy', 'aboutus', 'btrendie');
		$userCheck = Session::read('userLogin');
		if (empty($userCheck) && !in_array($path[0], $allowed)) {
			$this->redirect('/');
		}
		$this->render(join('/', $path));
	}

}

?>