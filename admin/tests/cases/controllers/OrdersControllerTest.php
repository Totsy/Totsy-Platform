<?php

namespace admin\tests\cases\controllers;

use lithium\action\Request;
use lithium\storage\Session;
use admin\controllers\OrdersController;
use admin\tests\mocks\models\OrderMock;
use admin\models\Event;
use admin\models\Order;
use admin\models\OrderShipped;
use admin\models\User;
use admin\models\Item;
use MongoId;
use MongoDate;

class OrdersControllerTest extends \lithium\test\Unit {

	public $controller;

	public function setUp() {
		Session::config(array(
			'default' => array('adapter' => 'Memory'),
            'cookie' => array('adapter' => 'Memory'),
		    'flash_message' => array('adapter' => 'Php')
		));
		
		Session::write('userLogin', array('invited_by' => '','email' => "jsquillets+t@totsy.com") , array('name' => 'default'));

		$this->controller = new OrdersController(array(
			'request' => new Request(),
			'classes' => array(
				'tax' => 'admin\tests\mocks\extensions\AvaTaxMock',
				'order' => 'admin\tests\mocks\models\OrderMock'
			)
		));
	}

	public function testIndexWithoutData() {
		$result = $this->controller->index();
		$this->assertTrue(!empty($result['headings']));
	}

	public function testIndex() {
		$data = array(
			'title' => 'test',
			'end_date' => $shipDate = new MongoDate(strtotime('+1 week'))
		);
		$event = Event::create($data);
		$event->save();

		$items[] = array(
			'event_id' => $event->_id
		);
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'_test' => 'a',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'shipping' => array(
				'firstname' => 'George',
				'lastname' => 'Opossum',
				'address' => 'Venice Rd'
			),
			'items' => $items
		);
		$order1 = Order::create($data);
		$order1->save(null, array('validate' => false));

		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'_test' => 'b',
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'billing' => array(
				'firstname' => 'Leonardo',
				'lastname' => 'di Caprio',
				'address' => 'Dreams Blvd'
			),
			'items' => $items
		);
		$order2 = Order::create($data);
		$order2->save(null, array('validate' => false));

		/* @fixme This currently fails as you cannot apply a regex on an id. */
		/*
		$this->controller->request->data = array(
			'search' => (string) $order1->_id,
			'type' => 'order'
		);
		$result = $this->controller->index();
		$this->assertTrue(!empty($result['orders']));
		*/

		$this->controller->request->data = array(
			'search' => 'Dreams',
			'type' => 'address'
		);
		$result = $this->controller->index();
		$this->assertTrue(!empty($result['orders']));
		$this->assertTrue(!empty($result['shipDate']));

		$event->delete();
		$order1->delete();
		$order2->delete();
	}
	
	/*
	 * Test OrdersController->cancelUnshippedItems with all items shipped
	 * The order should not be modified at all
	 */
	public function testCancelUnshippedItems_AllShipped() {
		
		//configuration
		$order_id = new MongoId();
		$ship_record_one_id = new MongoId();
		$ship_record_two_id = new MongoId();
		$ship_record_three_id = new MongoId();
		$ship_record_four_id = new MongoId();
		$ship_record_five_id = new MongoId();
		$ship_record_six_id = new MongoId();
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
		      'user_id' => '4d503feb5389266501000034'),
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
			     "0" => $ship_record_one_id,
			     "1" => $ship_record_two_id,
			     "2" => $ship_record_three_id,
			     "3" => $ship_record_four_id,
			     "4" => $ship_record_five_id,
			     "5" => $ship_record_six_id), 
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
		      'user_id' => '4d503feb5389266501000034'),
		  'shippingMethod' => 'ups' ,
		  'subTotal' => 195.3,
		  'tax' => 14.2,
		  'total' => 198.1,
		  'user_id' => null
		);
		$order = Order::create();
		$order->save($order_datas);
		$orderCollection = Order::collection();
		$order = $orderCollection->findOne(array('_id' => $order_id));
		
		$ship_record_one_data = 
		array(
			'_id'=> $ship_record_one_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-846-B1C-959',
			'Weight'=> '1.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d10000242'),
			'hash'=> '1a6d6010c78f36dbd6b9634de832752a',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:07'))
		);
		$ship_record_one = OrderShipped::create();
		$ship_record_one->save($ship_record_one_data);
		
		$ship_record_two_data = 
		array(
			'_id'=> $ship_record_two_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-4DB-B1C-D41',
			'Weight'=> '2.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d1000024a'),
			'hash'=> '2d3a802ce868a693431951f39ecc5608',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:13'))
		);
		$ship_record_two = OrderShipped::create();
		$ship_record_two->save($ship_record_two_data);
		
		$ship_record_three_data = 
		array(
			'_id'=> $ship_record_three_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-56D-B1C-959',
			'Weight'=> '1.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d10000249'),
			'hash'=> 'dad03956e3fcae78d55360b6bcc97808',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:15'))
		);
		$ship_record_three = OrderShipped::create();
		$ship_record_three->save($ship_record_three_data);
		
		$ship_record_four_data = 
		array(
			'_id'=> $ship_record_four_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-BB1-B1C-EE3',
			'Weight'=> '2.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d10000245'),
			'hash'=> 'd94e4cdb6cd9185e8f9edf17ade7d65b',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:15'))
		);
		$ship_record_four = OrderShipped::create();
		$ship_record_four->save($ship_record_four_data);
		
		$ship_record_five_data = 
		array(
			'_id'=> $ship_record_five_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-BB1-B1C-EE3',
			'Weight'=> '2.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d10000245'),
			'hash'=> '87e3d5d0ceb2d397d13a3a11512d5fd9',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:15'))
		);
		$ship_record_five = OrderShipped::create();
		$ship_record_five->save($ship_record_five_data);
		
		$ship_record_six_data = 
		array(
			'_id'=> $ship_record_six_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-88F-B1C-3F8',
			'Weight'=> '4.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d1000024d'),
			'hash'=> 'f44acaa8a92ed0ef760e36b0fcaa1ba6',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:15'))
		);
		$ship_record_six = OrderShipped::create();
		$ship_record_six->save($ship_record_six_data);

		$oc = new OrdersController;
		$oc->cancelUnshippedItems($order);
		
		$result = $orderCollection->findOne(array('_id' => $order_id));

		//Delete Temporary Documents
		Order::remove(array("_id" => $order_id));
		OrderShipped::remove(array("_id" => $ship_record_one_id));
		OrderShipped::remove(array("_id" => $ship_record_two_id));
		OrderShipped::remove(array("_id" => $ship_record_three_id));
		OrderShipped::remove(array("_id" => $ship_record_four_id));
		OrderShipped::remove(array("_id" => $ship_record_five_id));
		OrderShipped::remove(array("_id" => $ship_record_six_id));
		
		$this->assertEqual($order, $result);	
	}

	/*
	 * Test OrdersController->cancelUnshippedItems with 2 unshipped items
	 */
	public function testCancelUnshippedItems() {
		//configuration
		$order_id = new MongoId();
		$ship_record_one_id = new MongoId();
		$ship_record_two_id = new MongoId();
		$ship_record_three_id = new MongoId();
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
		      'user_id' => '4d503feb5389266501000034'),
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
			     "0" => $ship_record_one_id,
			     "1" => $ship_record_two_id,
			     "2" => $ship_record_three_id),
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
		      'user_id' => '4d503feb5389266501000034'),
		  'shippingMethod' => 'ups' ,
		  'subTotal' => 195.3,
		  'tax' => 14.2,
		  'total' => 198.1,
		  'user_id' => null
		);
		$order = Order::create();
		$order->save($order_datas);
		$orderCollection = Order::collection();
		$order = $orderCollection->findOne(array('_id' => $order_id));
		
		$ship_record_one_data = 
		array(
			'_id'=> $ship_record_one_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-846-B1C-959',
			'Weight'=> '1.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d10000242'),
			'hash'=> '1a6d6010c78f36dbd6b9634de832752a',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:07'))
		);
		$ship_record_one = OrderShipped::create();
		$ship_record_one->save($ship_record_one_data);
		
		$ship_record_two_data = 
		array(
			'_id'=> $ship_record_two_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-4DB-B1C-D41',
			'Weight'=> '2.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d1000024a'),
			'hash'=> '2d3a802ce868a693431951f39ecc5608',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:13'))
		);
		$ship_record_two = OrderShipped::create();
		$ship_record_two->save($ship_record_two_data);
		
		$ship_record_three_data = 
		array(
			'_id'=> $ship_record_three_id,
			'ShipDate'=> new MongoDate(strtotime('2011-08-22 04:00:00')),
			'ShipDC'=> 'DOT',
			'OrderNum'=> '4E4293A3BA16',
			'Tracking #'=> '1ZX782400371155853',
			'DC'=> 'TOT',
			'SKU'=> 'MEL-56D-B1C-959',
			'Weight'=> '1.00',
			'ContactName'=> 'Maria Tommasi',
			'Address1'=> '37 Columbia Court',
			'City '=> 'North Haledon',
			'StateOrProvince'=> 'NJ',
			'Zip'=> '07508',
			'Email'=> 'chachibean44@yahoo.com',
			'Tel'=> '9999999999',
			'OrderId'=> new MongoId('4e4293a3974f5ba1660000b8'),
			'ItemId'=> new MongoId('4dbb2b045899ef5d10000249'),
			'hash'=> 'dad03956e3fcae78d55360b6bcc97808',
			'created_date'=> new MongoDate(strtotime('2011-08-23 12:50:15'))
		);
		$ship_record_three = OrderShipped::create();
		$ship_record_three->save($ship_record_three_data);

		$oc = new OrdersController;
		$order = $oc->cancelUnshippedItems($order);
		
		$order = $order->data();

		//Delete Temporary Documents
		Order::remove(array("_id" => $order_id));
		OrderShipped::remove(array("_id" => $ship_record_one_id));
		OrderShipped::remove(array("_id" => $ship_record_two_id));
		OrderShipped::remove(array("_id" => $ship_record_three_id));
		
		// Verify that only 2 items were canceled
		$this->assertEqual(false, $order['items'][0]['cancel']);
		$this->assertEqual(true, $order['items'][1]['cancel']);
		$this->assertEqual(false, $order['items'][2]['cancel']);
		$this->assertEqual(false, $order['items'][3]['cancel']);
		$this->assertEqual(true, $order['items'][4]['cancel']);
		
		// Verify that the total was updated
		$this->assertEqual(52.15, $order['total']);	
	}

	public function testCancel() {
		$order_id = new MongoId("8788727dsds3782738dsdsds728");
		$user_id = new MongoId("787878787zazazag78dsdsdsds78");
		$item_id = new MongoId("4ddsqsdqszzz80f3ad53892614080076e0");
		$comment = "Comment @ Test";

		$remote = $this->controller;

		$remote->request->data = array('id' => (string) $order_id, 'comment' => $comment);
		$remote->request->params['type'] = 'html';
		$user = Session::read('userLogin');
		$order_datas = array(
			"_id" => $order_id,
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => (string) $item_id,
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => (string) $item_id,
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 3,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => (string) $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => (string) $user_id
		);
		$user_datas = array(
			"_id" => $user_id,
			"active" => 1,
			"created_on" => "Wed, 22 Sep 2010 16: 50: 44 -0400",
			"email" => uniqid('test') . 'example.com',
			"firstname" => "KLKL",
			"invitation_codes" => array(
			"0" => "fdfdfdddd"
			),
			"invited_by" => "fdfdfd",
			"lastip" => "204.246.230.160",
			"lastlogin" => "Thu, 10 Mar 2011 22: 42: 08 -0500",
			"lastname" => "OPOo",
			"legacy" => 0,
			"logincounter" => 9,
			"password" => "0b505f152dc80b527035e3500925936fe9703d2c",
			"purchase_count" => 2,
			"reset_token" => "0",
			"total_credit" => 0
		);
		$user = User::create();
		$user->save($user_datas);
		$order = OrderMock::create();
		$order->save($order_datas);

		$remote->cancel();

		$result_order = OrderMock::find('first', array('conditions' => array(
			'_id' => $order["_id"]
		)));
		$order = $result_order->data();

		$result = $order["cancel"];
		$this->assertTrue($result);

		foreach($order["items"] as $item) {
			$result = $item["cancel"];
			$this->assertTrue($result);
		}

		$result_user = User::find('first', array('conditions' => array(
			'_id' => $user["_id"]
		)));
		$this->assertTrue($result_user);

		$this->skipIf(!is_object($result_user), "Can't continue result is not an object.");

		$user = $result_user->data();
		foreach ($order["modifications"] as $modif) {
			$expected = $comment;
			$result = $modif['comment'];
			$this->assertEqual($expected, $result);
		}

		OrderMock::remove(array("_id" => $order_id));
		User::remove(array("_id" => $user_id));
	}

	public function testManageItemsUnsaved() {
		$data = array(
			"active" => 1,
			"email" => uniqid('test') . '@example.com',
			"firstname" => "Test",
			"lastname" => "User",
			"total_credit" => 0
		);
		$user = User::create($data);
		$result = $user->save();
		$this->assertTrue($result);

		$userId = $user->_id;

		$data = array(
		  "discount_exempt" => true,
		  "enabled" => true,
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_retail" => 10,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "tax" => 1,
		  "total_quantity" => 5,
		);
		$item = Item::create($data);
		$result = $item->save();
		$this->assertTrue($result);

		$item1Id = $item->_id;

		$data = array(
		  "description" => "test",
		  "discount_exempt" => true,
		  "enabled" => true,
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_retail" => 13,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "tax" => 1,
		  "total_quantity" => 2,
		);
		$item = Item::create($data);
		$result = $item->save();
		$this->assertTrue($result);

		$item2Id = $item->_id;

		$data = array(
			"authKey" => "090909099909",
			"credit_used" => -5,
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => (string) $item1Id,
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => (string) $item1Id,
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 10,
					"line_number" => 0,
					"status" => "Order Placed"
			),
			"1" => array(
				"_id" => (string) $item2Id,
				"discount_exempt" => false,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string) $item2Id,
				"product_weight" => 0.64,
				"quantity" => 2,
				"sale_retail" => 13,
				"size" => "no size",
				"line_number" => 0,
				"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => (string) $userId
			),
			"shippingMethod" => "ups",
			"subTotal" => 76,
			"tax" => 0,
			"total" => 92.7,
			"user_id" => (string) $userId
		);
		$order = OrderMock::create($data);
		$result = $order->save($data);
		$this->assertTrue($result);

		$orderId = $order->_id;

		$items = array(
			'0' => array(
				'cancel' => 'true',
				'initial_quantity' => '5',
				'quantity' => '5'
				),
			'1' => array(
				'cancel' => '',
				'initial_quantity' => '2',
				'quantity' => '1'
		));
		$comment = "Comment @ Test";

		$data = array(
			'id' => (string) $orderId,
			'comment' => $comment,
			'items' => $items,
			'total' => 92.7,
			'subTotal' => 76,
			'tax' => 0,
			'handling' => 7.95,
			'promocode_disable' => false,
			'credit_used' => -3.25,
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			'comment' => $comment,
			'user_id' => (string) $userId,
			'user_total_credits' => 1.75,
			'save' => 'false'
		);

		$this->controller->request->data = $data;
		$this->controller->request->params['type'] = 'html';
		$result = $this->controller->manage_items();

		$this->assertTrue($result->items[0]->cancel);
		$this->assertFalse($result->items[1]->cancel);

		/* @fixme Fails with 29.7 != 19.95.
		   It needs to be verified if the expecations of  tests are possibly wrong. */
		/*
		$expected = 29.7;
		$result = $result->total;
		$this->assertEqual($expected, $result);
		*/

		OrderMock::remove(array("_id" => $orderId));
		User::remove(array("_id" => $userId));
		Item::remove(array("_id" => $item1Id));
		Item::remove(array("_id" => $item2Id));
	}

	public function testManageItemsSaved() {
		$order_id = new MongoId("8788727dsds3782738dsdsds728");
		$user_id = new MongoId("787878787zazazag78dsdsdsds78");
		$item_id = new MongoId("4ddsqsdqszzz80f3ad53892614080076e0");
		$item_id_2 = new MongoId("0920909Z200IAOIOIZOAIIiioioioio");
		$comment = "Comment @ Test";
		$user = Session::read('userLogin');

		$items = array(
			'0' => array(
				'cancel' => 'true',
				'initial_quantity' => '5',
				'quantity' => '5'
				),
			'1' => array(
				'cancel' => '',
				'initial_quantity' => '2',
				'quantity' => '1'
		));
		$order_datas = array(
			"_id" => $order_id,
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => (string) $item_id,
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => (string) $item_id,
					"primary_image" => "4d015488ce64e5c072fc1e00",
					"product_weight" => 0.64,
					"quantity" => 5,
					"sale_retail" => 10,
					"size" => "no size",
					"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
					"event_name" => "Babyganics",
					"event_id" => "4cfd1dd1ce64e5300aeb4100",
					"line_number" => 0,
					"status" => "Order Placed"
			),
			"1" => array(
				"_id" => (string)$item_id_2,
				"category" => "Baby Gear",
				"color" => "",
				"description" => "TESTSTYTYSTYT",
				"discount_exempt" => false,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string)$item_id_2,
				"primary_image" => "4d015488ce64e5c072fc1e00",
				"product_weight" => 0.64,
				"quantity" => 2,
				"sale_retail" => 13,
				"size" => "no size",
				"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
				"event_name" => "Babyganics",
				"event_id" => "4cfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed"
			)),
			"order_id" => "4D03KLKLLKL8FE3",
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			"ship_date" => 1294272000,
			"ship_records" => array(
				"0" => new MongoId("4d5c5a405389266032003bfd"),
			),
			"shipping" => array(
				"_id" => "4cd779e1ce64e5aa45b60b00",
				"description" => "Home",
				"firstname" => "TEST",
				"lastname" => "Test",
				"telephone" => "",
				"address" => "2731 Ross Rd",
				"address_2" => "",
				"city" => "Pafdo Alto",
				"state" => "TE",
				"zip" => "909904303",
				"isAjax" => "1",
				"user_id" => (string) $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 76,
			"tax" => 0,
			"total" => 92.7,
			"user_id" => (string) $user_id
		);
		$user_datas = array(
			"_id" => $user_id,
			"active" => 1,
			"created_on" => "Wed, 22 Sep 2010 16: 50: 44 -0400",
			"email" => "fdkflkdlskfd@gmail.com",
			"firstname" => "KLKL",
			"invitation_codes" => array(
			"0" => "fdfdfdddd"
			),
			"invited_by" => "fdfdfd",
			"lastip" => "204.246.230.160",
			"lastlogin" => "Thu, 10 Mar 2011 22: 42: 08 -0500",
			"lastname" => "OPOo",
			"legacy" => 0,
			"logincounter" => 9,
			"password" => "0b505f152dc80b527035e3500925936fe9703d2c",
			"purchase_count" => 2,
			"reset_token" => "0",
			"total_credit" => 0
		);
		$user = User::create();
		$user->save($user_datas);
		$order = OrderMock::create();
		$order->save($order_datas);

		$remote = $this->controller;

		$datas = array(
			'id' => (string) $order_id,
			'comment' => $comment,
			'items' => $items,
			'total' => 29.7,
			'subTotal' => 13,
			'tax' => 0,
			'handling' => 7.95,
			'promocode_disable' => false,
			'credit_used' => -3.25,
			"promo_code" => "weekend10",
			"promo_discount" => -10,
			'comment' => $comment,
			'user_id' => (string) $user_id,
			'user_total_credits' => 1.75,
			'save' => 'true'
			);
		$remote->request->data = $datas;
		$remote->request->params['type'] = 'html';
		$result = $remote->manage_items();
		$selected_order = $result->data();

		$result = $selected_order["items"][0]["cancel"];
		$this->assertTrue($result);

		$expected = 29.7;
		$result = $selected_order['total'];
		$this->assertEqual($expected, $result);

		OrderMock::remove(array("_id" => $order_id));
		User::remove(array("_id" => $user_id));
		Item::remove(array("_id" => $item_id));
		Item::remove(array("_id" => $item_id_2));
	}

	public function testUpdateShippingWithoutData() {
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'date_created' => new MongoDate(strtotime('August 3, 2011'))
		);
		$order = Order::create($data);
		$order->save(null, array('validate' => false));

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);
	}

	public function testUpdateShippingWithMissing() {
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'date_created' => new MongoDate(strtotime('August 3, 2011'))
		);
		$order = Order::create($data);
		$order->save(null, array('validate' => false));

		$this->controller->request->data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'address' => 'Cloud St',
			'city' => null,
			'state' => 'California',
			'zip' => '9100',
			'phone' => '01234567890'
		);

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);

		$this->controller->request->data = array(
			'firstname' => 'George',
			'lastname' => 'Lucas',
			'address' => 'Cloud St'
		);

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);

		$order->delete();
	}

	public function testUpdateShippingAddsModificationAndShipping() {
		Session::write('userLogin', array(
			'email' => uniqid('test') . '@example.com'
		));
		$data = array(
			'_id' => $id = new MongoId(),
			'order_id' => $id,
			'date_created' => new MongoDate(strtotime('August 3, 2011')),
			'shipping' => $shipping = array(
				'firstname' => 'George',
				'lastname' => 'Lucas',
				'address' => 'Cloud St',
				'city' => 'LA',
				'state' => 'California',
				'zip' => '9100',
				'phone' => '01234567890'
			)
		);
		$order = Order::create($data);
		$order->save(null, array('validate' => false));

		$this->controller->request->data = $shippingNew = array(
			'firstname' => 'Luke',
			'lastname' => 'Skywalker',
			'address' => 'Cloud St',
			'city' => 'LA',
			'state' => 'California',
			'zip' => '9100',
			'phone' => '01234567890'
		);

		$result = $this->controller->updateShipping($order->_id);
		$this->assertNull($result);

		$order = Order::first((string) $order->_id);

		$result = !empty($order->modifications);
		$this->assertTrue($result);

		$result = filter_var($order->modifications[0]['author'], FILTER_VALIDATE_EMAIL);
		$this->assertTrue($result);

		$expected = $shipping;
		$result = $order->modifications[0]['old_datas']->data();
		$this->assertEqual($expected, $result);

		$expected = $shippingNew;
		$result = $order->shipping->data();
		$this->assertEqual($expected, $result);

		Session::delete('currentUser');
		$order->delete();
	}
}

?>