<?php

namespace admin\tests\cases\models;

use admin\models\Order;
use admin\models\User;
use admin\models\Item;
use MongoId;

class OrderTest extends \lithium\test\Unit {

	/*
	* Testing the Cancel Method of the Order
	*/
	public function testCancel() {
		//Configuration Test
		$author = "test";
		$comment = "commment @test !";
		$user_id = "787878787zazazag78dsdsdsds78";
		$order_id = "8788727dsds3782738dsdsds728";
		//Create temporary documents
		$remote = new Order();
		$order_datas = array(
			"_id" => new MongoId($order_id),
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => "4d038da6ce64e5973e8a1500",
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => "4cffa43ace64e5ae3e181900",
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
				"user_id" => $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => $user_id
		);
		$user_datas = array(
			"_id" => new MongoId($user_id),
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
		$order = Order::create();
		$order->save($order_datas);
		$user = User::create();
		$user->save($user_datas);
		//Request the tested method
		$remote->cancel((string)$order["_id"], $author, $comment);
		//Check Datas Order
		$check = true;
		$result_order = Order::find('first', array('conditions' => array(
			'_id' => $order["_id"]
		)));
		$order = $result_order->data();
		if($order["cancel"] != true) {
			$check = false;
		}
		foreach($order["items"] as $item) {
			if($item["cancel"] != true){
				$check = false;
			}
		}
		//Check Datas User
		$result_user = User::find('first', array('conditions' => array(
			'_id' => $user["_id"]
		)));
		$user = $result_user->data();
		$check_user = false;
		foreach($order["modifications"] as $modif)
		{
			if($modif["comment"] == $comment) {
				$check_user = true;
			}
		}
		if($check_user == false) {
			$check = false;
		}
		//Delete Temporary Documents
		Order::remove(array("_id" => $order_id));
		//Test result
		$this->assertEqual( true , $check);
	}
	
	/*
	* Testing the shipping Method of the Order
	*/
	public function testShipping() {
		$item_id = new MongoId("4ddsqsdqszzz80f3ad53892614080076e0");
		//Create temporary documents
		$remote = new Order();
		$items = array(
			"0" => array(
				"_id" => new MongoId("4ddsqsdqszzz80f3ad53892614080076e0"),
				"category" => "Room D\u00e9cor",
				"color" => "",
				"description" => "test",
				"discount_exempt" => true,
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
				"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed"
		));
		$item_datas = array(
		  "_id" => $item_id,
		  "category" => "Room D\u00e9cor",
		  "color" => "",
		  "created_date" => "Wed, 16 Mar 2011 13:30:21 -0400",
		  "description" => "test",
		  "details" => array (
		    "no size" => 3
		  ),
		  "discount_exempt" => true,
		  "enabled" => true,
		  "event" => array(
		    "4cfdfdfdfdfd1dd1ce64e5300aeb4100"
		  ),
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_details" => array(
		    "no size" => array(
		      "sale_count" => 1
		    )
		  ),
		  "sale_retail" => 168,
		  "shipping_exempt" => true,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "total_quantity" => 3,
		  "url" => "url_test",
		  "vendor" => "fdeeee",
		  "vendor_style" => "SDDSER.SO16",
		  "views" => 3
		);
		$item = Item::create();
		$item->save($item_datas);
		//Request the tested method
		$cost = $remote->shipping($items);
		//Delete Temporary Documents
		Item::remove(array("_id" => $item["_id"]));
		//Test result
		$this->assertEqual( 0 , $cost);
	}

	/*
	* Testing the OverSizeShipping Method of the Order
	*/
	public function testOverSizeShipping() {
		$item_id = new MongoId("4ddsqsdqszzz80f3ad53892614080076e0");
		//Create temporary documents
		$remote = new Order();
		$items = array(
			"0" => array(
				"_id" => new MongoId("4ddsqsdqszzz80f3ad53892614080076e0"),
				"category" => "Room D\u00e9cor",
				"color" => "",
				"description" => "test",
				"discount_exempt" => true,
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
				"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed"
		));
		$item_datas = array(
		  "_id" => $item_id,
		  "category" => "Room D\u00e9cor",
		  "color" => "",
		  "created_date" => "Wed, 16 Mar 2011 13:30:21 -0400",
		  "description" => "test",
		  "details" => array (
		    "no size" => 3
		  ),
		  "discount_exempt" => true,
		  "enabled" => true,
		  "event" => array(
		    "4cfdfdfdfdfd1dd1ce64e5300aeb4100"
		  ),
		  "modified_date" => "Wed, 16 Mar 2011 16:16:54 -0400",
		  "percent_off" => 0.3,
		  "product_dimensions" => "20x16 inches",
		  "product_weight" => 0,
		  "sale_details" => array(
		    "no size" => array(
		      "sale_count" => 1
		    )
		  ),
		  "sale_retail" => 168,
		  "shipping_exempt" => false,
		  "shipping_oversize" => "1",
		  "shipping_rate" => 6,
		  "shipping_weight" => 0,
		  "taxable" => true,
		  "total_quantity" => 3,
		  "url" => "url_test",
		  "vendor" => "fdeeee",
		  "vendor_style" => "SDDSER.SO16",
		  "views" => 3
		);
		$item = Item::create();
		$item->save($item_datas);
		//Request the tested method
		$cost = $remote->overSizeShipping($items);
		//Delete Temporary Documents
		Item::remove(array("_id" => $item["_id"]));
		//Test result
		$this->assertEqual( 6 , $cost);
	}
	
	/*
	* Testing the Tax Method of the Order
	*/
	public function testTax() {
		$item_id = new MongoId("4ddsqsdqszzz80f3ad53892614080076e0");
		$order_id = "8788727dsds3782738dsdsds728";
		$user_id = "787878787zazazag78dsdsdsds78";
		//Create temporary documents
		$items = array(
			"0" => array(
				"_id" => new MongoId("4ddsqsdqszzz80f3ad53892614080076e0"),
				"category" => "Room D\u00e9cor",
				"color" => "",
				"description" => "test",
				"discount_exempt" => true,
				"expires" => array(
					"sec" => 1292079402,
					"usec" => 0
				),
				"item_id" => (string) $item_id,
				"primary_image" => "4d015488ce64e5c072fc1e00",
				"product_weight" => 0.64,
				"quantity" => 5,
				"cancel" => false,
				"sale_retail" => 3,
				"size" => "no size",
				"url" => "babyganics-alcohol-free-hand-sanitizer-250ml",
				"event_name" => "Babyganics",
				"event_id" => "4cfdfdfdfdfd1dd1ce64e5300aeb4100",
				"line_number" => 0,
				"status" => "Order Placed",
				"tax" => 1,
				"taxable" => true
		));
		$remote = new Order();
		$current_order = array(
			"_id" => new MongoId($order_id),
			"authKey" => "090909099909",
			"credit_used" => -5,
			"date_created" => "Sat, 11 Dec 2010 09: 51: 15 -0500",
			"handling" => 7.95,
			"items" => array(
				"0" => array(
					"_id" => "4d038da6ce64e5973e8a1500",
					"category" => "Baby Gear",
					"color" => "",
					"description" => "BabyGanics Alcohol Free Hand Sanitizer 250ml",
					"discount_exempt" => false,
					"expires" => array(
						"sec" => 1292079402,
						"usec" => 0
					),
					"item_id" => "4cffa43ace64e5ae3e181900",
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
				"zip" => "11211",
				"isAjax" => "1",
				"user_id" => $user_id
			),
			"shippingMethod" => "ups",
			"subTotal" => 56.7,
			"tax" => 0,
			"total" => 49.65,
			"user_id" => $user_id
		);
		//Request the tested method
		$result = $remote->tax($current_order, $items);
		//Delete Temporary Documents
		Order::remove(array("_id" => $order_id));
		//Test result
		$this->assertEqual( 3 , $result);
	}
}

?>