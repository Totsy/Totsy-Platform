<?php

namespace app\controllers;

use \app\models\Cart;
use \app\models\User;
use \lithium\storage\Session;
use app\models\Affiliate;
use MongoRegex;

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
		$invited_by = NULL;
		 if ($userInfo) {
			$user = User::find('first', array(
				'conditions' => array('_id' => $userInfo['_id']),
				'fields' => array('invited_by')
			));
			if($user){
			    if($user->invited_by){
			        $invited_by = $user->invited_by;
			    }
			}
		}


        if(preg_match('/join/',$_SERVER['REQUEST_URI'])) {

		    $invited_by = substr($_SERVER['REQUEST_URI'],6);
        }
	    $pixel = $this->getPixels('invidence');
		$this->set(compact('pixel'));


		$this->_render['layout'] = 'main';
		parent::_init();
	}

	protected function getPixels($invited_by) {
	  if(!($invited_by)) return null;

         $url = $_SERVER['REQUEST_URI'];

        if(preg_match('(/orders/view/)',$url)) {
            $url = '/orders/view';
        }

		$options = array('conditions' => array(
		                        'invitation_codes' => $invited_by,
								'pixel' => array(
									'$elemMatch'=>array(
										'page' =>$url,
										'enable' => true
								))), 'fields'=>array('pixel.pixel' => 1, 'pixel.page' => 1));
		$pixels = Affiliate::find('all', $options );
		$pixels= $pixels->data();
		$pixel = NULL;

		foreach($pixels as $data) {
			foreach($data['pixel'] as $index) {

                if(in_array($url, $index['page'])) {
				    $pixel .= '<br>\n'. $index['pixel']. '<br>';
				}
			}
		}

		return $pixel;
	}
}

?>