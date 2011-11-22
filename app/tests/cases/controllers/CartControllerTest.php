<?php

namespace app\tests\cases\controllers;

use lithium\action\Request;
use app\controllers\CartController;
use app\models\Cart;
use app\models\Event;
use app\models\Item;
use MongoDate;
use lithium\storage\Session;
use li3_fixtures\test\Fixture;

class CartControllerTest extends \lithium\test\Unit {

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

	/*
	* Testing the Update method from the CartController
	*/
	public function testCartUpdate() {
		$post = array('cart' => array(
			'200001' => '4',
			'200002' => '5'
		));
		$response = new Request(array(
			'data' => $post,
			'params' => array('controller' => 'carts', 'action' => 'update')
		));
		$cartPuppet = new CartController(array('request' => $response));
		$cartPuppet->update();
		$result1 = Cart::find('first', array('conditions' => array('_id' => '200001')));
		$result2 = Cart::find('first', array('conditions' => array('_id' => '200002')));
		$this->assertEqual(4, $result1->quantity,'Uh-oh! Update was not successful: ' .
		 'Expected value: 4 not equal to cart 20001 quantity: ' . $result1->quantity);
		$this->assertNotEqual(5, $result2->quantity,'Uh-oh! Update was successful: ' .
		'Expected value: 5 not equal to cart 20002 quantity: ' . $result2->quantity);
	}

	/*
	* Testing the Remove method from the CartController
	*/
	public function testRemove() {
		//Configuration Test
		$cart_id = "787878787zazazag7878";
		$request = new Request(array(
			'data' => array('id' => $cart_id),
<<<<<<< HEAD
			'params' => array('controller' => null, 'action' => null, 'type' => 'html')
=======
			'type' => 'html',
			'params' => array('controller' => 'carts', 'action' => 'update')
>>>>>>> 5c3d636... Provide all params for test request object.
		));
		$remote = new CartController(compact('request'));
		$user = Session::read('userLogin');
		$active_time = new MongoDate();
		$expire_time = new MongoDate();
		$expire_time->sec = ($expire_time->sec + (60 * 60 * 60));
		//Create temporary document
		$cart_data = array(
			"_id" => $cart_id,
			"category" => "bath&fefdsfsdfdsfsded",
			"color" => "",
			"created" => $active_time,
			"description" => "FireREEEman Towel",
			"discount_exempt" => false,
			"event" =>  array(
							"0" => "YFY7FD7YF7YD7HUHU"
						),
			"expires" => $expire_time,
			"item_id" => "87887273782738728",
			"primary_image" => "4d6b0a185389264b5fdsfsd090903001140",
			"product_weight" => 1,
			"quantity" => 10,
			"sale_retail" => 19.8,
			"session" => "test",
			"size" => "M",
			"url" => "fireman-towel",
			"user" => $user['_id'],
			"vendor_style" => "KIFFDSDSDSDFIRETOW" );
		$cart = Cart::create();
		$cart->save($cart_data);
		//Request the tested method
		$result = $remote->remove();
		//Test result
		$this->assertEqual(0, $result["cartcount"] );
		Cart::remove(array('_id' => $cart_id ));
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
}

?>