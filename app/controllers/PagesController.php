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
		}elseif ($path[0] == 'btrendie' || $path[0] == "living_social"){
			$this->_render['layout'] = 'blank';
		}
		$allowed = array('terms', 'faq', 'contact', 'privacy', 'aboutus', 'btrendie', 'moms', 'testimonials', 'being_green', 'press','affiliates','living_social');
		$userCheck = Session::read('userLogin');
		if (empty($userCheck) && !in_array($path[0], $allowed)) {
			$this->redirect('/');
		}
		if (in_array($path[0], $allowed) && $path[0] == "living_social") {
		    $today = date('m/d/Y');
		    if (!(($today >= '06/27/2011') && ($today <= '08/01/2011'))) {
		        $this->redirect('/');
		    }

		}
		$this->render(array('template' => $path[0]));
	}

}

?>