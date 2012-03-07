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
 * AvaTax implementation for the aadmin app (there is another one for front-end app).
 */
class AvaTax extends \lithium\core\StaticObject {

	protected static $_settings = array();

	/**
	 * Switcher for avalara/totsy tax calculation system
	 *
	 */
	protected static $useAvatax = true;

	public static function __init() {
		$settings = Environment::get(true);
		static::$_settings = $settings['avatax'];
	}

	public static function cancelTax($order,$tryNumber=0){
		try{
			AvaTaxWrap::commitTax($order);
			AvaTaxWrap::cancelTax($order);
		} catch (Exception $e) {
			/* @fixme The line below has been added in order to prevent
			   undefined variable errors. Obviously this can't be a real
			   fix and possible results untintended behavior. However it's
		       not clear what has been the intended behavior intially. */
			$data = array();

			// Try again or return 0;
			BlackBox::tax('can not process tax cancelation via avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );

			if ($tryNumber <= static::$_settings['retriesNumber']){
				return self::getTax($data, ++$tryNumber);
			} else {
				Mailer::send('TaxProcessError', static::$_settings['logEmail'], array(
					'message' => 'Avatax system is unreachable. Can NOT process tax canselation.<br>'.
								 'SERVER: '.php_uname('n') ,
					'reason' => $e->getMessage(),
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s').
								'<br><div style="padding-left:15px;">'.
									'SERVER: '.php_uname('n').'<br>'.
									'DATE: '.date('Y-m-d H:i:s').'<br>'.
									'INFO: '.print_r($data,true).'<br>'.
									'TRACE: '.$e->getTraceAsString().
								'</div>',
					'info' => $order
				));
				return 0;
			}
		}
	}

	public static function getTax($data, $tryNumber = 0){
		if (isset(static::$_settings['useAvatax'])) {
			static::$useAvatax = static::$_settings['useAvatax'];
		}
		$data = (array) $data + array(
			'totalDiscount' => 0,
			'shippingAddr' => null,
			'billingAddr' => null
		);
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
		if (empty($data['shippingAddr']) && !empty($data['order']['shipping'])){
           $data['shippingAddr'] = $data['order']['shipping'];
        }
        if (empty($data['billingAddr']) && !empty($data['order']['billing'])){
           $data['billingAddr'] = $data['order']['billing'];
        }
		if (static::$useAvatax === false){
			return array(
				'tax'=>static::totsyCalculateTax($data),
				'avatax' => static::$useAvatax
			);
		}

		try{
			return array(
				'tax'=> (float) AvaTaxWrap::getTax($data),
				'avatax' => static::$useAvatax
			);
		} catch (Exception $e){
			// Try again or return 0;
			BlackBox::tax('can not calculate tax via avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber <= static::$_settings['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.static::$_settings['retriesNumber']);
				return self::getTax($data,++$tryNumber);
			} else {
				try {
					BlackBox::tax('Trying old way.');
					Mailer::send('TaxProcessError', static::$_settings['logEmail'], array(
						'message' => 'Avatax system was unreachable.<br>Tax calculation was performed internally using default state tax.<br>'.
									 'SERVER: '.php_uname('n'),
						'reason' => $e->getMessage(),
						'trace' => 'ADMIN @ '.date('Y-m-d H:i:s').
									'<br><div style="padding-left:15px;">'.
										'SERVER: '.php_uname('n').'<br>'.
										'DATE: '.date('Y-m-d H:i:s').'<br>'.
										'INFO: '.print_r($data,true).'<br>'.
										'TRACE: '.$e->getTraceAsString().
									'</div>',
						'info' => $data
					));
					return array(
						'tax'=>static::totsyCalculateTax($data),
						'avatax' => static::$useAvatax
					);
				} catch (Exception $m){
					BlackBox::tax('ERROR tax returns 0');
					Mailer::send('TaxProcessError', static::$_settings['logEmail'], array(
						'message' => 'Was unable to calculate tax. Charged $0 tax for this order.<br>'.
									 'SERVER: '.php_uname('n'),
						'reason' => $e->getMessage(),
						'trace' => 'ADMIN @ '.date('Y-m-d H:i:s').
									'<br><div style="padding-left:15px;">'.
										'SERVER: '.php_uname('n').'<br>'.
										'DATE: '.date('Y-m-d H:i:s').'<br>'.
										'INFO: '.print_r($data,true).'<br>'.
										'TRACE: '.$e->getTraceAsString().
									'</div>',
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
			if ($tryNumber <= static::$_settings['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.static::$_settings['retriesNumber']);
				return self::postTax($data,++$tryNumber);
			} else {
				BlackBox::tax('ERROR tax returns 0');
				Mailer::send('TaxProcessError', static::$_settings['logEmail'], array(
					'message' => 'Was unable to post tax.<br>'.
								 'SERVER: '.php_uname('n'),
					'reason' => $e->getMessage(),
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s').
								'<br><div style="padding-left:15px;">'.
									'SERVER: '.php_uname('n').'<br>'.
									'DATE: '.date('Y-m-d H:i:s').'<br>'.
									'INFO: '.print_r($data,true).'<br>'.
									'TRACE: '.$e->getTraceAsString().
								'</div>',
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
			if ($tryNumber <= static::$_settings['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.static::$_settings['retriesNumber']);
				return self::returnTax($data,++$tryNumber);
			} else {
				BlackBox::tax('ERROR tax returns 0');
				Mailer::send('TaxProcessError', static::$_settings['logEmail'], array(
					'message' => 'Was unable to process return tax.<br>'.
								 'SERVER: '.php_uname('n'),
					'reason' => $e->getMessage(),
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s').
								'<br><div style="padding-left:15px;">'.
									'SERVER: '.php_uname('n').'<br>'.
									'DATE: '.date('Y-m-d H:i:s').'<br>'.
									'INFO: '.print_r($data,true).'<br>'.
									'TRACE: '.$e->getTraceAsString().
								'</div>',
					'info' => $data
				));
				return 0;
			}
		}

	}

	public static function commitTax($data, $tryNumber=0){
		try{
			AvaTaxWrap::commitTax($data['order']);
		} catch (Exception $e) {
			BlackBox::tax('can not commit tax via avalara.');
			BlackBox::taxError($e->getMessage()."\n".$e->getTraceAsString() );
			if ($tryNumber <= static::$_settings['retriesNumber']){
				BlackBox::tax(($tryNumber+1).' attempt of '.static::$_settings['retriesNumber']);
				return self::commitTax($data,++$tryNumber);
			} else {
				BlackBox::tax('ERROR tax returns 0');
				Mailer::send('TaxProcessError', static::$_settings['logEmail'], array(
					'message' => 'Was unable to commit tax.<br>'.
								 'SERVER: '.php_uname('n'),
					'reason' => $e->getMessage(),
					'trace' => 'ADMIN @ '.date('Y-m-d H:i:s').
								'<br><div style="padding-left:15px;">'.
									'SERVER: '.php_uname('n').'<br>'.
									'DATE: '.date('Y-m-d H:i:s').'<br>'.
									'INFO: '.print_r($data,true).'<br>'.
									'TRACE: '.$e->getTraceAsString().
								'</div>',
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