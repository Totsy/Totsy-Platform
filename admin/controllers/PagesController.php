<?php

namespace admin\controllers;

use admin\models\User;
use admin\models\Menu;
use lithium\storage\Session;

class PagesController extends \lithium\action\Controller {

	public function view() {
		$path = func_get_args();
		if (empty($path)) {
			$path = array('home');
		}
		$this->_render['layout'] = 'main';
		$this->render(join('/', $path));
	}
	
	/**
	 * Set the userInfo for use in all the views
	 */	
	protected function _init() {
		parent::_init();
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$userInfo = Session::read('userLogin');
			$self->set(compact('userInfo'));
			return $chain->next($self, $params, $chain);
		});
	}
	
}

?>