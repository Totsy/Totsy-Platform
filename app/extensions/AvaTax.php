<?php 
namespace app\extensions;

use lithium\analysis\Logger;
use lithium\core\Environment;
use lithium\action\Request;
use app\extensions\Mailer;
use app\extensions\BlackBox;
use app\models\Cart;
use AvaTaxWrap;
use Exception;

/**
 * 
 * AvaTax implementation for the app (there is another one for admin app)
 *
 */

class AvaTax {
	
	/**
	 * Switcher for avalara/totsy tax calculation system
	 * 
	 */
	protected static $useAvatax = true;
	
	public static function getTax($data,$tryNumber=0){
		$settings = Environment::get(Environment::get());
		if (isset($settings['avatax']['useAvatax'])) { static::$useAvatax = $settings['avatax']['useAvatax']; }
		
		if ( is_array($data) && array_key_exists('cartByEvent',$data)){
			$data['items'] = static::getCartItems($data['cartByEvent']);
			static::shipping($data);
		} 
		$data['totalDiscount'] = 0;
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
			$tax = 	AvaTaxWrap::getTax($data);
			if (is_object($tax) && (get_class($tax) =='Exception' || get_class($tax) =='SoapFault')){
				throw  new Exception($tax->getMessage());
			}
			$return = array( 
				'tax' => $tax,
				'avatax' => static::$useAvatax
			);
		} catch (Exception $e){
			
			// if we got an error try $settings['avatax']['retriesNumber'] times then
			// try to do it with default totsy tax calculation functon 
			// On error return '0'
			
			// Try again or return 0;
			BlackBox::tax('can not calculate tax via avalara.');
			
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber < $settings['avatax']['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.$settings['avatax']['retriesNumber']);
				$return = self::getTax($data,++$tryNumber);
			} else {
				
				if (isset($data['taxCart'])){
					BlackBox::tax('Trying old way.');
					
					Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
						'message' => 'Avatax system was unreachable',
						'reason' => $e->getMessage(),
						'result' => 'Tax calculation was performed internally using default state tax.',
						'trace' => '<br><div style="padding-left:15px;">'.
										'DATE: '.date('Y-m-d H:i:s').'<br>',
										'INFO: '.$data.'<br>',
										'TRACE: '.$e->getTraceAsString()
					));
					$return = array( 
						'tax'=>static::totsyCalculateTax($data),
						'avatax' => false
					);
				} else {
					BlackBox::tax('ERROR tax returns 0');
					Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
						'message' => 'Was unable to calculate tax',
						'reason' => $e->getMessage(),
						'result' => 'Charged $0 tax for this order.',
						'trace' => '<br><div style="padding-left:15px;">'.
										'DATE: '.date('Y-m-d H:i:s').'<br>',
										'INFO: '.$data.'<br>',
										'TRACE: '.$e->getTraceAsString()
					));
					
					$return = array( 
						'tax'=>0,
						'avatax' => false
					);
				}
			}
		}
		return $return;
	} 
	
  	public static function postTax($data,$tryNumber=0){
  		$settings = Environment::get(Environment::get());
  		if (is_array($data) && array_key_exists('cartByEvent',$data) ){
			$data['items'] = static::getCartItems($data['cartByEvent']);
			static::shipping($data);
		}  		
  		$data['admin'] = 1;
  		try {
  			$return = AvaTaxWrap::getAndCommitTax($data);
  		} catch (Exception $e){
			// Try again or return 0;
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber <= $settings['avatax']['retriesNumber']){
				$return = self::postTax($data,++$tryNumber);
			} else {
				Mailer::send('TaxProcessError', $settings['avatax']['logEmail'], array(
					'message' => 'Was unable to calculate tax',
					'reason' => $e->getMessage(),
					'result' => 'Charged $0 tax for this order.',
					'trace' => '<br><div style="padding-left:15px;">'.
									'DATE: '.date('Y-m-d H:i:s').'<br>',
									'INFO: '.$data.'<br>',
									'TRACE: '.$e->getTraceAsString()
				));
				$return = 0;
			}
		}
		return $return;
  	}
	
  	private static function totsyCalculateTax ($data) {
  		if (!array_key_exists('overShippingCost',$data)) { $data['overShippingCost'] = 0; }
  		if (!array_key_exists('shippingCost',$data)) { $data['shippingCost'] = 0; }
  		$tax = array_sum($data['taxCart']->tax($data['shippingAddr']));
  		return $tax ? $tax + (($data['overShippingCost'] + $data['shippingCost']) * Cart::TAX_RATE) : 0;
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