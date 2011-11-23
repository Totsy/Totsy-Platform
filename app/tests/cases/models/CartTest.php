<?php

namespace app\tests\cases\models;

use app\models\Cart;
use MongoId;
use app\models\Item;
use app\models\Event;
use li3_fixtures\test\Fixture;

class CartTest extends \lithium\test\Unit {

    public function setUp() {
 		$efixture = Fixture::load('Event');
		$ifixture = Fixture::load('Item');
		$cfixture = Fixture::load('Cart');
		$next = $efixture->first();
		do {
			Event::remove(array('_id' => $next['_id'] ));
			$event = Event::create();
			$event->save($next);
		} while ($next = $efixture->next());

		$next = $ifixture->first();

		do {
			Item::remove(array('_id' => $next['_id'] ));
			$item = Item::create();
			$item->save($next);
		} while ($next = $ifixture->next());

		$next = $cfixture->first();
		do {
			Cart::remove(array('_id' => $next['_id'] ));
			$cart = Cart::create();
			$cart->save($next);
		} while ($next = $cfixture->next());
	}

	public function tearDown() {
		$efixture = Fixture::load('Event');
		$ifixture = Fixture::load('Item');
		$cfixture = Fixture::load('Cart');

		$event = $efixture->first();
		do {
			Event::remove( array('_id' => $event['_id'] ) );
		} while ($event = $efixture->next());

		$item = $ifixture->first();
		do {
			Item::remove( array( '_id' => $item['_id'] ) );
		} while ($item = $ifixture->next());

		$cart = $cfixture->first();
		do {
			Cart::remove( array('_id' => $cart['_id'] ) );
		} while ($cart = $cfixture->next());
	}

	/*
	* Testing the Check Method of the Cart
	*/
	public function testCheck() {
		$quantity = 20;
		$cart_id = "787878787zazazag7878";
		$item_id = "87887273782738728";

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

		$result = $remote->check($quantity, $cart_id);

		Cart::remove(array("_id" => $cart_id));
		Item::remove(array("_id" => $item_id));

		$this->assertTrue($result["status"]);
	}
}

?>