<?php

namespace app\tests\cases\models;

use app\models\Cart;
use app\models\Item;
use MongoId;

class CartTest extends \lithium\test\Unit {

	/*
	* Testing the Check Method of the Cart
	*/
	public function testCheck() {
		//Configuration Test
		$quantity = 20;
		$cart_id = "787878787zazazag7878";
		$item_id = "87887273782738728";
		//Create temporary documents
		$remote = new Cart();
		$datas_cart = array(
			"_id" => $cart_id,
			"category" => "bath&feed",
			"color" => "",
			"created" => Date( "Mon Feb 28 00:48:02 2011" ),
			"description" => "Fireman Towel",
			"discount_exempt" => false,
			"event" =>  array(
							"0" => "4d63ee8c5389267b3c00ce96"
						),
			"expires" => Date( "Mon Feb 28 01:03:02 2011" ),
			"item_id" => $item_id,
			"primary_image" => "4d6b0a185389264b53001140",
			"product_weight" => 1,
			"quantity" => 1,
			"sale_retail" => 19.8,
			"session" => "o274fbl46ra2bggqh42ihcids1",
			"size" => "18-24M",
			"url" => "fireman-towel",
			"user" => "4d6b34965389264a530045b7",
			"vendor_style" => "KIDFIRETOW" );
		$cart = Cart::create();
		$cart->save($datas_cart);
		$datas_item = array(
			"_id" => $item_id,
			"total_quantity" => 112,
			"details" => array(
				"sub_category" => "",
			    "no size" => "",
			    "0-6M" => "",
			    "0-3M" => "",
			    "3-6M" => "",
			    "6-12M" => "",
			    "12-18M" => "",
			    "18-24M" => "27",
			    "2" => "38",
			    "3/4Y" => "13",
			    "5/6Y" => "34" )
		);
		$item = Item::create();
		$item->save($datas_item);
		//Request the tested method
		$result = $remote->check($quantity, $cart_id);
		//Delete Temporary Documents
		Cart::remove(array("_id" => $cart_id));
		Item::remove(array("_id" => $item_id));
		//Test result
		$this->assertEqual( true , $result["statut"]);
	}
}

?>