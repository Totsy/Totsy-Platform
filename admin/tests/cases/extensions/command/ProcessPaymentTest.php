<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\ProcessPayment;
use admin\models\Item;
use admin\models\Order;
use MongoDate;
use MongoId;

class ProcessPaymentTest extends \lithium\test\Unit {
	/**
	 * Test the run function
	 */
	public function testRun() {
		
		//configuration
		$order_id = new MongoId("4e7a37cfc24efc3107000269");
		//Create temporary documents
		$remote = new Order();
		$order_datas = 
		array(
		  '_id' => $order_id,
		  'authKey' => '3789571045', 
		  'auth_error' => null,
		  'avatax' => true,
		  'billing' => 
		    array(
		      '_id' => '4e4293975899efaa5c0000bb' ,
		      'description' => 'Home' ,
		      'firstname' => 'Maria' ,
		      'lastname' => 'Tommasi' ,
		      'telephone' => '' ,
		      'address' => '37 Columbia Court' ,
		      'address_2' => '' ,
		      'city' => 'North Haledon' ,
		      'state' => 'NJ' ,
		      'zip' => '07508' ,
		      'isAjax' => '1' ,
		      'user_id' => '4e1f5e1c5899ef1c4e0001ed'),
		  'card_number' => '6840' ,
		  'card_type' => 'visa' ,
		  'date_created' => '2011-08-10T14: 20: 19.0Z' ,
		  'handling' => 7.95,
		  'items' => 
		    array(
		      "0" => 
		        array(
		          '_id' => '4e429272974f5bb36a0064e7' ,
		          'category' => 'Accessories' ,
		          'color' => 'Blue' ,
		          'description' => 'Trunki Terrance' ,
		          'discount_exempt' => false,
		          'expires' => '',
		          'item_id' => '4e4149615899efe21c000115' ,
		          'primary_image' => '4dbb2ec1d6b0259742000075' ,
		          'product_weight' => 3.8,
		          'quantity' => 1,
		          'sale_retail' => 28,
		          'size' => 'no size' ,
		          'url' => 'trunki-terrance-blue' ,
		          'event_name' => 'Trunki by Melissa and Doug' ,
		          'event_id' => '4e402aa55899efad12000014' ,
		          'line_number' => 0,
		          'status' => 'Order Placed' ),
		      "1" => 
		        array(
		          '_id' => '4e4292de5899ef675d0000b3' ,
		          'category' => 'Accessories' ,
		          'color' => 'Red' ,
		          'description' => 'Trunki Ruby' ,
		          'discount_exempt' => false,
		          'expires' => '',
		          'item_id' => '4e4149615899efe21c000118' ,
		          'primary_image' => '4dbb2e64d6b025994200006a' ,
		          'product_weight' => 3.8,
		          'quantity' => 4,
		          'sale_retail' => 28,
		          'size' => 'no size' ,
		          'url' => 'trunki-ruby-red' ,
		          'event_name' => 'Trunki by Melissa and Doug' ,
		          'event_id' => '4e402aa55899efad12000014' ,
		          'line_number' => 1,
		          'status' => 'Order Placed' ),
		      "2" => 
		        array(
		          '_id' => '4e429238d6b02585160000e3' ,
		          'category' => 'Accessories' ,
		          'color' => '' ,
		          'description' => 'Trunki Alphabet Stickers' ,
		          'discount_exempt' => false,
		          'expires' => '',
		          'item_id' => '4e4149615899efe21c00011d' ,
		          'primary_image' => '4dbb2dbbd6b025ca3f000074' ,
		          'product_weight' => 0.05,
		          'quantity' => 2,
		          'sale_retail' => 1.4,
		          'size' => 'no size' ,
		          'url' => 'trunki-alphabet-stickers' ,
		          'event_name' => 'Trunki by Melissa and Doug' ,
		          'event_id' => '4e402aa55899efad12000014' ,
		          'line_number' => 2,
		          'status' => 'Order Placed' ),
		      "3" => 
		        array(
		          '_id' => '4e429250d6b0256416001eb6' ,
		          'category' => 'Accessories' ,
		          'color' => 'Blue' ,
		          'description' => 'Trunki Saddlebag ' ,
		          'discount_exempt' => false,
		          'expires' => '',
		          'item_id' => '4e4149615899efe21c00011c' ,
		          'primary_image' => '4dbb2ddcd6b0258842000073' ,
		          'product_weight' => 0.45,
		          'quantity' => 1,
		          'sale_retail' => 10.5,
		          'size' => 'no size' ,
		          'url' => 'trunki-saddlebag-blue' ,
		          'event_name' => 'Trunki by Melissa and Doug' ,
		          'event_id' => '4e402aa55899efad12000014' ,
		          'line_number' => 3,
		          'status' => 'Order Placed' ),
		      "4" => 
		        array(
		          '_id' => '4e42925cd6b0256416001eb7' ,
		          'category' => 'Accessories' ,
		          'color' => 'Orange/Red' ,
		          'description' => 'Trunki Saddlebag' ,
		          'discount_exempt' => false,
		          'expires' => '',
		          'item_id' => '4e4149625899efe21c000120' ,
		          'primary_image' => '4dbb2d1cd6b0258d4200006a' ,
		          'product_weight' => 0.45,
		          'quantity' => 4,
		          'sale_retail' => 10.5,
		          'size' => 'no size' ,
		          'url' => 'trunki-saddlebag-orange-red' ,
		          'event_name' => 'Trunki by Melissa and Doug' ,
		          'event_id' => '4e402aa55899efad12000014' ,
		          'line_number' => 4,
		          'status' => 'Order Placed')),
		  'modifications' => 
		    array(
		    	"0" =>
		        array(
		          'author' => null,
		          'type' => 'items' ,
		          'date' => '2011-09-12T16: 10: 28.0Z' ,
		          'comment' => 'Canceling unshipped items')),
		  'order_id' => null,
		  'overSizeHandling' => 0,
		  'payment_date' => '2011-08-25T12: 14: 00.753Z' ,
		  'promo_code' => '' ,
		  'promo_discount' => '' ,
		  'promocode_disable' => true,
		  'service' => 
		    array(),
		  'ship_date' => '2011-09-06T04: 00: 00.0Z' ,
		  'ship_records' => 
		    array(
		      "0" => new MongoId('4e53a1ff974f5b9c08001769') ,
		      "1" => new MongoId('4e53a205974f5b9c0800176c') ,
		      "2" => new MongoId('4e53a207974f5b9c0800176d')),
		  'shipping' => 
		    array(
		      '_id' => '4e4293975899efaa5c0000bb' ,
		      'description' => 'Home' ,
		      'firstname' => 'Maria' ,
		      'lastname' => 'Tommasi' ,
		      'telephone' => '' ,
		      'address' => '37 Columbia Court' ,
		      'address_2' => '' ,
		      'city' => 'North Haledon' ,
		      'state' => 'NJ' ,
		      'zip' => '07508' ,
		      'isAjax' => '1' ,
		      'user_id' => '4e1f5e1c5899ef1c4e0001ed'),
		  'shippingMethod' => 'ups' ,
		  'subTotal' => 195.3,
		  'tax' => 14.2,
		  'total' => 198.1,
		  'user_id' => '4e1f5e1c5899ef1c4e0001ed'
		);
		$order = Order::create();
		$order->save($order_datas);
		
		$remote = new ProcessPayment();
		
		$remote->run();
		
		// order 1 has a short shipped item that should get canceled and removed from the order total
		$orderCollection = Order::collection();
		$result = $orderCollection->findOne(array('_id' => $order_id));

		//Delete Temporary Documents
		Order::remove(array("_id" => $order_id));
		
		$expected = 52.15;
		$this->assertEqual($expected, $result->total,'Short shipped order was not charged correctly: ' .
		 'Expected value: '.$expected.' not equal to order 1 total: ' . $result->total);
		
	}
}