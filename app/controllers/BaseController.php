<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\User;
use \lithium\storage\Session;

/**
* The base controller will setup functionality used throughout the app.
*/
class BaseController extends \lithium\action\Controller {

	/**
	 * Get the userinfo for the rest of the site from the session.
	 */
	protected function _init() {
		$userInfo = Session::read('userLogin');
		$this->set(compact('userInfo'));
		$cartCount = Cart::itemCount();

		if ($userInfo) {
			$user = User::find('first', array(
				'conditions' => array('_id' => $userInfo['_id']),
				'fields' => array('total_credit')
			));
			if ($user) {
				$decimal = ($user->total_credit < 1) ? 2 : 0;
				$credit = ($user->total_credit > 0) ? number_format($user->total_credit, $decimal) : 0;
			}
		}
		$this->set(compact('cartCount', 'credit'));
		$this->_render['layout'] = 'main';
		parent::_init();
	}
}

?>