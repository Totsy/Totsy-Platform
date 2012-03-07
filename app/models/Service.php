<?php
namespace app\models;

use lithium\storage\Session;
use app\models\Cart;

class Service extends Base {

    public $_meta = array('source' => 'services');

    /**
    * If the user's session is marked eligible for the freeshipping service
    * it will be applied to the order
    * @param float shipping cost
    * @param float order sized handling
    * @return array of shipping and oversized handling
    **/
    public static function freeShippingCheck($shippingCost = 7.95, $overSizeHandling = 0.00, $isOnlyDigital = false) {
        $enable = false;
        $service = Session::read('services', array('name' => 'default'));
		#Never Apply FreeShipping to Large Items
		$shippingCost = 7.95;
		$overSizeHandling = 0.00;
		if(Session::read('layout', array('name'=>'default'))!=="mamapedia") {
			if ( $service && array_key_exists('freeshipping', $service)) {
			    if ($service['freeshipping'] === 'eligible' && !$isOnlyDigital) {
			    	Cart::updateSavings(null, 'services', ($shippingCost + $overSizeHandling));
					$enable = true;
				} else {
					$shippingCost = 0;
					$overSizeHandling = 0;
				}			
			}
		} 		 
		
		return compact('shippingCost', 'overSizeHandling', 'enable');
    }

    /**
    * If the users's session is marked eligible for $10 dollars
    * @param float subtotal
    * @return float
    **/
    public static function tenOffFiftyCheck($subTotal) {
        $savings = 0.00;
        $service = Session::read('services', array('name' => 'default'));
                
		if(Session::read('layout', array('name'=>'default'))!=="mamapedia") {
			if ( $service && array_key_exists('10off50', $service)) {
			    if ($service['10off50'] === 'eligible') {
			        if ((float) $subTotal >= 50.00) {
			            $savings = 10.00;
			            Cart::updateSavings(null, 'services', $savings);
			        }
				}
			}
		}
		return $savings;
    }

}

?>