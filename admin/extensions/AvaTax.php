<?php 
namespace admin\extensions;

use lithium\analysis\Logger;
use lithium\core\Environment;
use lithium\action\Request;
use admin\extensions\Mailer;
use admin\extensions\BlackBox;
use AvaTaxWrap;
use Exception;


/**
 * 
 * AvaTax implementation for the aadmin app (there is another one for front-end app)
 *
 */

class AvaTax {
	
	/**
	 * Switcher for avalara/totsy tax calculation system
	 * 
	 */
	protected static $useAvatax = true;
	
	public static function  cancelTax($order,$tryNumber=0){
		try{
			AvaTaxWrap::commitTax($order);
			AvaTaxWrap::cancelTax($order);
		} catch (Exception $e) {

			// Try again or return 0;
			BlackBox::tax('can not process tax cancelation via avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				return self::getTax($data,++$tryNumber);
			} else {
				Mailer::send('TaxProcessError', $setting['avatax']['logEmail'], array(
					'message' => 'Avatax system is unreachable. Can NOT process tax canselation' ,
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s'),
					'info' => $order
				));
				return 0;
			}
		}
	}
	
	public static function getTax($data,$tryNumber=0){
		$settings = Environment::get(Environment::get());
		if (isset($settings['avatax']['useAvatax'])) { static::$useAvatax = $settings['avatax']['useAvatax']; }
		
		
		$data['totalDiscount'] = 0;
		if ( is_array($data) && array_key_exists('cartByEvent',$data)){
			$data['items'] = static::getCartItems($data['cartByEvent']);
			static::shipping($data);
		} 
		if (is_object($data['shippingAddr'])){ $data['shippingAddr'] = $data['shippingAddr']->data(); }
		if (is_object($data['billingAddr'])){ $data['billingAddr'] = $data['billingAddr']->data(); }
		if (array_key_exists('orderPromo',$data)){
			$data['totalDiscount'] = $data['totalDiscount'] + $data['orderPromo']->saved_amount;
			unset($data['orderPromo']);
		}
		if (array_key_exists('orderCredit',$data)){
			$data['totalDiscount'] = $data['totalDiscount'] + $data['orderCredit']->credit_amount;
			unset($data['orderCredit']);
		}
		if (array_key_exists('orderServiceCredit',$data)){
			$data['totalDiscount'] = $data['totalDiscount'] + abs($data['orderServiceCredit']);
			unset($data['orderServiceCredit']);
		}

		if (static::$useAvatax === false){
			return array( 
				'tax'=>static::totsyCalculateTax($data),
				'avatax' => static::$useAvatax
			);			
		}
		
		try{	
			return array( 
				'tax'=> AvaTaxWrap::getTax($data),
				'avatax' => static::$useAvatax
			);	
		} catch (Exception $e){
			// Try again or return 0;
			BlackBox::tax('can not calculate tax via avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.$settings['avatax']['retriesNumber']);
				return self::getTax($data,++$tryNumber);
			} else {
				try {
					BlackBox::tax('Trying old way.');
					Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
						'message' => 'Avatax system was unreachable.<br>Tax calculation was performed internally using default state tax.',
						'trace' => 'ADMIN @ '.date('Y-m-d H:i:s'),
						'info' => $data
					));	
					return array( 
						'tax'=>static::totsyCalculateTax($data),
						'avatax' => static::$useAvatax
					);
				} catch (Exception $m){
					BlackBox::tax('ERROR tax returns 0');
					Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
						'message' => 'Was unable to calculate tax. Charged $0 tax for this order.',
						'trace' => 'ADMIN @ '.date('Y-m-d H:i:s'),
						'info' => $data
					));
					return 0;		
				}
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
  			BlackBox::tax('can not post tax to avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			// Try again or return 0;
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.$settings['avatax']['retriesNumber']);
				return self::postTax($data,++$tryNumber);
			} else {
				BlackBox::tax('ERROR tax returns 0');
				Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
					'message' => 'Was unable to post tax.',
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s'),
					'info' => $data
				));
				return 0;
			}
		}
  	}
	
  	public static function returnTax($data,$tryNumber=0){
  		$data['doctype'] = 'return';

  		try{		
	  		static::getTax($data);
			static::commitTax($data['order']['order_id']);
		} catch (Exception $e){
			BlackBox::tax('can not return tax via avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.$settings['avatax']['retriesNumber']);
				return self::returnTax($data,++$tryNumber);
			} else {
				BlackBox::tax('ERROR tax returns 0');
				Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
					'message' => 'Was unable to process return tax.',
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s'),
					'info' => $data
				));
				return 0;
			}
		}
		
	}
	
	public static function  commitTax($data,$tryNumber=0){
		try{
			AvaTaxWrap::commitTax($order);
		} catch (Exception $e) {
			BlackBox::tax('can not commit tax via avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.$settings['avatax']['retriesNumber']);
				return self::commitTax($data,++$tryNumber);
			} else {
				BlackBox::tax('ERROR tax returns 0');
				Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
					'message' => 'Was unable to commit tax.',
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s'),
					'info' => $data
				));
				return 0;
			}
		}
	}
	
  	private static function totsyCalculateTax ($data) {
  		if (!array_key_exists('overShippingCost',$data)) { $data['overShippingCost'] = 0; }
  		if (!array_key_exists('shippingCost',$data)) { $data['shippingCost'] = 0; }
  		
  		$tax = array_sum($data['ordermodel']::tax($data['current_order'],$data['itms']));
  		return $tax ? $tax + (($data['overShippingCost'] + $data['shippingCost']) * $data['ordermodel']::TAX_RATE) : 0;
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