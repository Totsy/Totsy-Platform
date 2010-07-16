<?php

namespace app\controllers;

use \app\models\Cart;
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
		$cart = Cart::all(array(
			'conditions' => array(
				'session' => Session::key()),
			'fields' => array('item.quantity')
		));
		$cartCount = 0;
		if (!empty($cart)) {
			foreach ($cart as $item) {
				$cartCount += $item->item->quantity;
			}
		}

		$this->set(compact('cartCount'));
		$this->_render['layout'] = 'main';
		parent::_init();
	}
	
	
}
