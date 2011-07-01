<?php 
namespace app\extensions;

use lithium\analysis\Logger;
use lithium\core\Environment;
use lithium\action\Request;
use li3_silverpop\extensions\Silverpop;
use AvaTaxWrap;

/**
 * 
 * AvaTax implementation for the app (there is another one for admin app)
 *
 */

class AvaTax {
	
	public static function getTax($data,$tryNumber=0){
		if ( is_array($data) && array_key_exists('cartByEvent',$data)){
			$data['items'] = static::getCartItems($data['cartByEvent']);
			static::shipping($data);
		} 
		if (is_object($data['shippingAddr'])){ $data['shippingAddr'] = $data['shippingAddr']->data(); }
		if (is_object($data['billingAddr'])){ $data['billingAddr'] = $data['billingAddr']->data(); }
		$settings = Environment::get(Environment::get());
		try{		
			return AvaTaxWrap::getTax($data);
		} catch (Exception $e){
			// Try again or return 0;
			Logger::error($e->getMessage()."\n".$e->getTraceAsStirng() );
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				return self::getTax($data,++$tryNumber);
			} else {
				Silverpop::send('TaxProcessError', array(
					'email' => $setting['avatax']['logEmail'],
					'message' => $e->getMessage(),
					'trace' => $e->getTraceAsStirng(),
					'info' => $data
				));
				return 0;
			}
		}
	} 
	
  	public static function postTax($data,$tryNumber=0){
  		
  		if (is_array($data) && array_key_exists('cartByEvent',$data) ){
			$data['items'] = static::getCartItems($data['cartByEvent']);
			static::shipping($data);
		}  		
  		$data['admin'] = 1;
  		try {
  			return AvaTaxWrap::getAndCommitTax($data);
  		} catch (Exception $e){
			// Try again or return 0;
			Logger::error($e->getMessage()."\n".$e->getTraceAsStirng() );
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				return self::postTax($data,++$tryNumber);
			} else {
				Silverpop::send('TaxProcessError', array(
					'email' => $setting['avatax']['logEmail'],
					'message' => $e->getMessage(),
					'trace' => $e->getTraceAsStirng(),
					'info' => $data
				));
				return 0;
			}
		}
  	}
	
	protected static function getCartItems($cartByEvent){
		$items = array();
		foreach ($cartByEvent as $key => $event){
			foreach ($event as $item){
				$items[]=$item;
			}
		}
		return $items;
	}
	
	protected static function shipping (&$data){
		if (array_key_exists('shippingCost', $data) && $data['shippingCost']>0 ){
			$data['items'][] = array(
				'_id' => 'Shipping',
				'item_id' => 'Shipping',
				'category' => 'Shipping',
				'description' => 'shipping',
				'quantity' => 1,
				'sale_retail' => $data['shippingCost'],
				'taxIncluded' => true
			);	
		}

		if (array_key_exists('overShippingCost', $data) && $data['overShippingCost']>0 ){
			$data['items'][] = array(
				'_id' => 'OverShipping',
				'item_id' => 'OverShipping',
				'category' => 'Shipping',
				'description' => 'Over shipping',
				'quantity' => 1,
				'sale_retail' => $data['overShippingCost'],
				'taxIncluded' => true
			);	
		}
	} 
}

?>