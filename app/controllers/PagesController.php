<?php

namespace app\controllers;
use app\extensions\helper\Menu;
use app\models\Navigation;

class PagesController extends \lithium\action\Controller {

	public function view() {
		$path = func_get_args();
		if (empty($path)) {
			$path = array('home');
		}
				
		$this->_render['layout'] = 'main';
		$this->render(join('/', $path));
	}
	
	
}

?>