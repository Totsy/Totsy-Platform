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
	
		$this->applyFilter('__invoke',  function($self, $params, $chain) {
			$menu = Menu::find('all', array(
				'conditions' => array(
					'location' => 'about', 
					'active' => 'true'
			)));
			$self->set(compact('menu'));
			return $chain->next($self, $params, $chain);
		});
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