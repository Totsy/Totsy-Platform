<?php

namespace app\controllers;

use \lithium\storage\Session;
/**
* The base controller will setup functionality used throughout the app.
*/
class BaseController extends \lithium\action\Controller
{
	/**
	 * Get the userinfo for the rest of the site from the session.
	 */
	protected function _init() {
		$userInfo = Session::read('userLogin');
		$this->set(compact('userInfo'));
		$this->_render['layout'] = 'main';
		parent::_init();
	}
}
