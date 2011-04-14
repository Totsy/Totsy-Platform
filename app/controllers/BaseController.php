<?php

namespace app\controllers;

use app\models\Cart;
use app\models\User;
use lithium\storage\Session;
use app\models\Affiliate;
use MongoRegex;
use li3_facebook\extension\FacebookProxy;
use lithium\core\Environment;

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
        User::setupCookie();
		$logoutUrl = (!empty($_SERVER["HTTPS"])) ? 'https://' : 'http://';
	    $logoutUrl = $logoutUrl."$_SERVER[SERVER_NAME]/logout";
		/**
		 * Setup all the necessary facebook stuff
		 */
		$this->fbsession = $fbsession = FacebookProxy::getSession();
		$fbconfig = FacebookProxy::config();
		$fblogout = FacebookProxy::getlogoutUrl(array('next' => $logoutUrl));
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
		$this->set(compact('cartCount', 'credit', 'fbsession', 'fbconfig', 'fblogout'));

		/**
		* Get the pixels for a particular url.
		**/
		$invited_by = NULL;
		 if ($userInfo) {
			$user = User::find('first', array(
				'conditions' => array('_id' => $userInfo['_id']),
				'fields' => array('invited_by')
			));
			if ($user){
				if ($user->invited_by){
					$invited_by = $user->invited_by;
			    }
			}
		}
		if (preg_match('/a/',$_SERVER['REQUEST_URI'])) {
			$invited_by = substr($_SERVER['REQUEST_URI'], 3);
			if (strpos($invited_by, '?')) {
				$invited_by = substr($invited_by, 0, strpos($invited_by, '?'));
			}
			if (strpos($invited_by, '&')) {
				$invited_by = substr($invited_by,0,strpos($invited_by, '&'));
			}
		}
		$pixel = Affiliate::getPixels($_SERVER['REQUEST_URI'], $invited_by);
		$pixel .= Session::read('pixel');
		Session::delete('pixel');
		$this->set(compact('pixel'));

		$this->_render['layout'] = 'main';
		parent::_init();
	}

	/**
	 * @param array $sessionInfo
	 * @return boolean
	 */
	public function writeSession($sessionInfo) {
		return (Session::write('userLogin', $sessionInfo, array('name'=>'default')));
	}

}

?>