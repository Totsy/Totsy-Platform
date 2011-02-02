<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\User;
use \lithium\storage\Session;
use app\models\Affiliate;

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

		/**
		* Get the pixels for a particular url.
		**/
		$options = array('conditions'=>array(
								'pixel'=>array(
									'$elemMatch'=>array(
										'page' => $_SERVER['REQUEST_URI'],
										'enable' => true
								))), 'fields'=>array('pixel.pixel'=>1));
		$pixels = Affiliate::find('all', $options );
		$pixels= $pixels->data();
		$data = NULL;

		foreach($pixels as $pixel){
			foreach($pixel['pixel'] as $index){
				$data .= implode('<br>', $index);
			}
		}
		$this->set(compact('data'));


		$this->_render['layout'] = 'main';
		parent::_init();
	}
}

?>