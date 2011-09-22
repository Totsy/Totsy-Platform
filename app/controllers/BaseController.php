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
* @see app/models/Affiliate::getPixels()
*/
class BaseController extends \lithium\action\Controller {

	public function __construct(array $config = array()) {
		/* Merge $_classes of parent. */
		$vars = get_class_vars('\lithium\action\Controller');
		$this->_classes += $vars['_classes'];

		parent::__construct($config);
	}

	/**
	 * Get the userinfo for the rest of the site from the session.
	 */
	protected function _init() {
		parent::_init();
	     if(!Environment::is('production')){
            $branch = "<h4 id='global_site_msg'>Current branch: " . $this->currentBranch() ."</h4>";
            $this->set(compact('branch'));
        }
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
				'fields' => array('total_credit', 'deactivated','affiliate_share')
			));
			if (isset($user) && $user) {
			    /**
			    * If the users account has been deactivated during login,
			    * destroy the users session.
			    **/
			    if ($user->deactivated == true) {
			        Session::clear(array('name' => 'default'));
			        Session::delete('appcookie', array('name' => 'cookie'));
		            FacebookProxy::setSession(null);
			    }
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

		 if (isset($user) && $user) {
			$cookie = Session::read('cookieCrumb', array('name'=>'cookie'));
			$userData = $user->data();
			if(is_array($cookie) && array_key_exists('affiliate',$cookie)){
                Affiliate::linkshareCheck($user->_id, $cookie['affiliate'], $cookie);
            }
            if (array_key_exists('invited_by',$userInfo)){
                $invited_by = $userInfo['invited_by'];
            }else if(array_key_exists('affiliate_share',$userData)){
                $invited_by = $userData['affiliate_share']['affiliate'];
            }
		}
		/**
		* If visitor lands on affliate url e.g www.totsy.com/a/afflilate123
		**/
		$affiliate = is_object($this->request);
		$affiliate = $affiliate && isset($this->request->params['controller']);
		$affiliate = $affiliate && isset($this->request->params['action']);
		$affiliate = $affiliate && $this->request->params['controller']  == 'affiliates';
		$affiliate = $affiliate && $this->request->params['action']  == 'register';
		$affiliate = $affiliate && empty($invited_by);

		if ($affiliate) {
			$invited_by = $this->request->args[0];
		}

		/**
		* Retrieve any pixels that need to be fired off
		**/
		if (is_object($this->request) && isset($this->request->url)){
			$url = $this->request->url;
		} else {
			$url = $_SERVER['REQUEST_URI'];
		}
		$pixel = Affiliate::getPixels($url, $invited_by);
		$pixel .= Session::read('pixel');
		/**
		* Remove pixel to avoid firing it again
		**/
		Session::delete('pixel');
		#Clean Credit Card Infos if out of Orders/CartController
		$this->CleanCC();
		/**
		* Send pixel to layout
		**/
		$this->set(compact('pixel'));

		$this->_render['layout'] = 'main';

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
               $created_date = (is_object($user->created_date)) ? $user->created_date->sec : strtotime($user->created_date);
             $dayThirty = date('m/d/Y',mktime(0,0,0,date('m',$created_date),
                    date('d',$created_date)+30,
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
                $created_date = (is_object($user->created_date)) ? $user->created_date->sec : strtotime($user->created_date);
                $dayThirty = date('m/d/Y',mktime(0,0,0,date('m',$created_date),
                    date('d',$created_date)+30,
                    date('Y',$created_date)
                ));
                if ( ($service->start_date->sec <= $created_date && $service->end_date->sec > $created_date) ) {
                    if ($user->purchase_count == 1) {
                        $firstOrder = Order::find('first' , array('conditions' => array('user_id' => $userInfo['_id'])));
                        $order_date = $firstOrder->date_created->sec;
                        $expire_date = date('m/d/Y H:i:s',mktime(0,0,0, date('m',$created_date),
                            date('d',$created_date) + 30,
                            date('Y',$created_date)
                        ));
                        /**
                        * Check if the offer is expired for this user
                        **/
                        if ((date('m/d/Y H:i:s', $order_date) < $dayThirty) && (date('m/d/Y H:i:s') < $expire_date)) {
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
	/**
	* Displays what git branch you are currently developing in
	**/
	public function currentBranch() {
		if (!is_dir($git = dirname(LITHIUM_APP_PATH) . '/.git')) {
			return;
		}
		$head = trim(file_get_contents("{$git}/HEAD"));
		$head = explode('/', $head);

		return array_pop($head);
	}

	/**
	* Clean Credits Card Infos if out of Cart/Orders/Search ??? Controller
	**/
	public function cleanCC() {
		$controllers = array('orders', 'cart', 'search');

		$clean = Session::check('cc_infos');
		$clean = $clean && is_object($this->request);
		$clean = $clean && isset($this->request->params['controller']);
		$clean = $clean && !in_array($this->request->params['controller'], $controllers);

		if ($clean) {
			Session::delete('cc_infos');
		}
	}
}

?>
