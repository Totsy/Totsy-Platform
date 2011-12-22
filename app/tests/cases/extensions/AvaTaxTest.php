<?php 

namespace app\tests\cases\extensions;

use Exception;
use MongoDate;
use MongoId;
use \app\extensions\AvaTax;    
use lithium\core\Environment;

class AvaTaxTest extends \lithium\test\Unit {
	
	public function testGetTaxQuote4TaxableOrder(){
		$order = $this->getOrderDetails();
		$tax = AvaTax::getTax($order);
		
		$this->assertEqual(true, is_array($tax),'Expecting tax result to be an array, '.gettype($tax).' instead');
		$this->assertEqual(true, $tax['avatax'],'Tax calculation performed internally.');
		$this->assertNotEqual(0, $tax['tax'],'Expecting tax amount to be greater then 0 , returned '.$tax['tax'].' insted.');
	}
	
	public function testGetTaxQuote4NonTaxableOrder(){
		$order = $this->getOrderDetails(false);
		$tax = AvaTax::getTax($order);
	
		$this->assertEqual(true, is_array($tax),'Expecting tax result to be an array, '.gettype($tax).' instead');
		$this->assertEqual(true, $tax['avatax'],'Tax calculation performed internally instead of AvaTax.');
		$this->assertEqual(0, $tax['tax'],'Expecting tax amount to be equal to 0, returned '.$tax['tax'].' insted.');
	}

	public function testPostTax4TaxableOrder(){
		
		$order['shippingAddr'] = $this->taxableShippingAddress();
		$order['billingAddr'] = $this->billingAddress();
		$order['order']['order_id'] = 'TST'.md5(microtime(true));
		$order['cart'] = $this->getTaxOrder();
		$tax = AvaTax::postTax($order);
		
		$this->assertNotEqual(0, $tax,'Expecting tax amount to be greater then 0 , returned '.$tax.' insted.');
	}
	
	
	public function testGetInternalTax4TaxableOrder(){
		// Set gonfig to do not use Avatax
		$origin = $internal = Environment::get(Environment::get());
		$internal['avatax']['useAvatax'] = false; 
		Environment::set(
			Environment::get(),
			array(
				'avatax' => $internal['avatax'] 
			)
		);
		unset($internal);
				
		$order = $this->getOrderDetails();
		$tax = AvaTax::getTax($order  + array( 'taxCart' => $this->getTaxCart()) );
		
		$this->assertEqual(true, is_array($tax),'Expecting tax result to be an array, '.gettype($tax).' instead');
		$this->assertEqual(false, $tax['avatax'],'Tax calculation performed by AvaTax instead of internal.');
		$this->assertNotEqual(0, $tax['tax'],'Expecting tax amoint to be greater then 0 , returned '.$tax['tax'].' insted.');
		
		// Reset config to irginal settings
		Environment::set(
			Environment::get(), 
			array(
				'avatax' => $origin['avatax'] 
			)
		);
		
	}
	
	public function testGetInternalTax4NonTaxableOrder(){
		// Set gonfig to do not use Avatax
		$origin = $internal = Environment::get(Environment::get());
		$internal['avatax']['useAvatax'] = false;
		
		Environment::set(
			Environment::get(),
			array(
				'avatax' => $internal['avatax'] 
			)
		);
		
		$order = $this->getOrderDetails(false);
		$tax = AvaTax::getTax($order  + array( 'taxCart' => $this->getTaxCart()) );
	
		$this->assertEqual(true, is_array($tax),'Expecting tax result to be an array, '.gettype($tax).' instead');
		$this->assertEqual(false, $tax['avatax'],'Tax calculation performed by AvaTax instead of internal.');
		$this->assertEqual(0, $tax['tax'],'Expecting tax amount  to be equal to 0, returned '.$tax['tax'].' insted.');
	
		// Reset config to irginal settings
		Environment::set(
			Environment::get(),
			array(
				'avatax' => $origin['avatax']  
			)
		);
	
	}
	
	private function getOrderDetails($isTaxable=true){
		return array (
  					'billingAddr' => $this->billingAddress(),
					'shippingAddr' => $isTaxable ? $this->taxableShippingAddress() : $this->nonTaxableShippingAddress() ,
					'shippingCost' => 7.95,
					'overShippingCost' => 0,
					'items' => array (
						array (
      						'_id' => md5(microtime(true).mt_rand()),
      						'category' => 'Apparel',
      						'color' => 'Black',
      						'description' => 'Short Sleeve Empire Dress',
      						'discount_exempt' => false,
      						'expires' => array (
        						'sec' => 1322776431,
        						'usec' => 0,
							),
      						'item_id' => md5(microtime(true).mt_rand()),
      						'miss_christmas' => '0',
      						'primary_image' => md5(microtime(true).mt_rand()),
      						'product_weight' => 0,
      						'quantity' => 1,
      						'sale_retail' => 39.25,
      						'size' => '6',
      						'url' => 'short-sleeve-empire-dress-black',
    					),
   						array (
							'_id' => 'Shipping',
      						'item_id' => 'Shipping',
      						'category' => 'Shipping',
      						'description' => 'shipping',
      						'quantity' => 1,
      						'sale_retail' => 7.95,
      						'taxIncluded' => true
    					)
  					),
  					'totalDiscount' => 0
				);
	}

	private function taxableShippingAddress(){
		return array (
				 'firstname' => 'Test',
				 'lastname' => 'Tester',
				 'telephone' => '1234567890',
				 'address' => '10 18th st.',
				 'address_2' => '',
				 'city' => 'New York',
				 'state' => 'NY',
				 'zip' => '10011'
	  		);
	}
	
	private function nonTaxableShippingAddress(){
		return array (
				 'firstname' => 'Test',
				 'lastname' => 'Tester',
				 'telephone' => '1234567890',
				 'address' => '30 Corbin Drive',
				 'address_2' => '',
				 'city' => 'Darien',
				 'state' => 'CT',
				 'zip' => '06820'
		);
	}
	
	public function billingAddress(){
		return array (
		    'firstname' => 'Slavik',
		    'lastname' => 'Koshelevskyy',
		    'telephone' => '1234567890',
		    'address' => '10 18th st.',
		    'address2' => '',
		    'city' => 'New York',
		    'state' => 'NY',
		    'zip' => '10011'
		);
	}
	
	private function getTaxOrder(){
		$doc = new \lithium\data\entity\Document ();
		$doc->set( $this->getTaxCart() );
		
		return $doc;
	} 
	
	private function getTaxCart(){

		$doc = new \lithium\data\entity\Document ();
		$doc->set(
			array (
				'_id' => new MongoId('4ed7f3ebc24efc9901004367'),
				'category' => 'Apparel',
				'color' => 'Black',
				'order_id' => 'TST'.md5(microtime(true)),
				'description' => 'Short Sleeve Empire Dress',
				'discount_exempt' => false,
				'expires' => new MongoDate(1322776431),
				'item_id' => '4ed505a1943e835a2c000014',
				'miss_christmas' => '0',
				'primary_image' => '4ed53915943e834b44000011',
				'product_weight' => 0,
				'quantity' => 1,
				'sale_retail' => 39.25,
				'size' => '6',
				'url' => 'short-sleeve-empire-dress-black',
				'event_name' => 'Pretty Me ',
				'event_url' => 'pretty-me',
				'event_id' => '4ed4ff3e943e830c27000011'
			)
		);
		
		return array($doc); 
	}
		
}

?>