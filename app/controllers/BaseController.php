<?php

namespace app\controllers;

use app\models\Cart;
use app\models\User;
use app\models\Order;
use app\models\Service;
use lithium\storage\Session;
use app\models\Affiliate;
use MongoRegex;
use li3_facebook\extension\FacebookProxy;
use lithium\core\Environment;

/**
* The base controller will setup functionality used throughout the app.
* @see app/models/Affiliate
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
	    $logoutUrl = $logoutUrl . "$_SERVER[SERVER_NAME]/logout";
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
		$this->freeShippingEligible($userInfo);
		$this->tenOffFiftyEligible($userInfo);
		/**
		* Get the pixels for a particular url.
		**/
		$invited_by = NULL;
		 if ($userInfo) {
			$user = User::find('first', array(
				'conditions' => array('_id' => $userInfo['_id'])
			));
			$cookie = Session::read('cookieCrumb', array('name'=>'cookie'));
			if(array_key_exists('affiliate',$cookie)){
                Affiliate::linkshareCheck($user->_id, $cookie['affiliate'], $cookie);
            }
			if ($user){
				if ($user->invited_by){
					$invited_by = $user->invited_by;
			    }else if($user->affiliate_share){
                    $invited_by = $user->affiliate_share['affiliate'];
                }
			}
		}
		/**
		* If visitor lands on affliate url e.g www.totsy.com/a/afflilate123
		**/
		if (preg_match('/a/',$_SERVER['REQUEST_URI'])) {
			$invited_by = substr($_SERVER['REQUEST_URI'], 3);
			if (strpos($invited_by, '?')) {
				$invited_by = substr($invited_by, 0, strpos($invited_by, '?'));
			}
			if (strpos($invited_by, '&')) {
				$invited_by = substr($invited_by,0,strpos($invited_by, '&'));
			}
		}
		/**
		* Retrieve any pixels that need to be fired
		**/
		$pixel = Affiliate::getPixels($_SERVER['REQUEST_URI'], $invited_by);
		$pixel .= Session::read('pixel');
		/**
		* Remove pixel to avoid firing it again
		**/
		Session::delete('pixel');
		/**
		* Send pixel to layout
		**/
		$this->set(compact('pixel'));

		$this->_render['layout'] = 'main';
		parent::_init();
	}

	/**
	* Services - checks if the user is eligible for free shipping service offer
	* @param array $userInfo - user session info
	* @see app/controllers/AffiliatesController
	* @see app/controllers/UsersController
	**/
	public function freeShippingEligible($userInfo){
	    $sessionServices = Session::read('services', array('name' => 'default'));
	    $service = Service::find('first', array('conditions' => array('name' => 'freeshipping') ));
	    if ($userInfo && $service) {
	        $user = User::find('first', array('conditions' => array('_id' => $userInfo['_id'])));
	        if ($user) {
                $created_date = $user->created_date->sec;
          /*   $dayThirty = date('m/d/Y',mktime(0,0,0,date('m',$created_date),
                    date('d',$created_date)+30,
                    date('Y',$created_date)
                )); */
                /**
                * NOTE: EXPIRATION DATE IS ACTUALLY 30 DAYS FROM THE FIRST PURCHASE NOT 15 MINUTES
                **/
                $dayThirty = date('m/d/Y H:i:s',mktime(
                    date('H',$created_date),
                    date('i',$created_date) + 15,
                    date('s', $created_date),
                    date('m',$created_date),
                    date('d',$created_date),
                    date('Y',$created_date)
                ));
	            /**
	            *   check if the user is still eligible for free shipping
	            *   criteria: User must have registered between the time the service
	            *   starts and end; and the user uses the service with in thirty days
	            *   of their registration
	            */

                if ( ($service->start_date->sec <= $created_date &&
                        $service->end_date->sec > $created_date) &&
                    (date('m/d/Y H:i:s') < $dayThirty)) {

                    //checks if the user ever made a purchase
                    if ($user->purchase_count < 1) {
                        $sessionServices = Session::read('services', array('name' => 'default'));
                        $sessionServices['freeshipping'] = 'eligible';
                        Session::write('services', $sessionServices, array('name' => 'default'));
                    } else {
                        $sessionServices['freeshipping'] = 'used';
                        Session::write('services', $sessionServices,array('name' => 'default'));
                    }
                } else { //mark freeshipping service as expired
                        $sessionServices['freeshipping'] = 'expired';
                        Session::write('services', $sessionServices,array('name' => 'default'));
                }
	        }
	    }
	}
	public function tenOffFiftyEligible($userInfo) {
	    $serviceSession = Session::read('services', array('name' => 'default'));
	    $service = Service::find('first', array('conditions' => array('name' => '10off50') ));

	    if ($userInfo && $service) {
	        $user = User::find('first', array('conditions' => array('_id' => $userInfo['_id'])));
            if ($user) {
                $created_date = $user->created_date->sec;
                if ( ($service->start_date->sec <= $created_date && $service->end_date->sec > $created_date) ) {
                    if ($user->purchase_count == 1) {
                        $firstOrder = Order::find('first' , array('conditions' => array('user_id' => $userInfo['_id'])));
                        $order_date = $firstOrder->date_created->sec;
                        /**
                        * NOTE: EXPIRATION DATE IS ACTUALLY 30 DAYS FROM THE FIRST PURCHASE NOT 15 MINUTES
                        **/
                        $expire_date = date('m/d/Y H:i:s',mktime(
                            date('H',$created_date),
                            date('i',$created_date) + 15,
                            date('s', $created_date),
                            date('m',$created_date),
                            date('d',$created_date),
                            date('Y',$created_date)
                        ));
                        /**
                        * Check if the offer is expired for this user
                        **/
                        if (date('m/d/Y H:i:s') < $expire_date) {
                            $serviceSession['10off50'] = 'eligible';
                            Session::write('services', $serviceSession,array('name' => 'default'));
                        } else {
                            $serviceSession['10off50'] = 'expired';
                            Session::write('services', $serviceSession,array('name' => 'default'));
                        }
                    } else {
                        $serviceSession['10off50'] = 'ineligible';
                        Session::write('services', $serviceSession,array('name' => 'default'));
                    }
                }
	        }
	    }
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