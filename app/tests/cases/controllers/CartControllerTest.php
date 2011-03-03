<?php

namespace app\tests\cases\controllers;

<<<<<<< HEAD
use lithium\action\Request;
use \app\controllers\CartController;
use \app\models\Cart;
use \app\models\Event;
use \app\models\Item;
use li3_fixtures\test\Fixture;


class CartControllerTest extends \lithium\test\Unit {


	public function setUp() {
        $efixture = Fixture::load('Event');
        $ifixture = Fixture::load('Item');
        $cfixture = Fixture::load('Cart');

        $event = Event::create();
        $event->save($efixture->first());
        $item = Item::create();
        $item->save($ifixture->first());
        $cart = Cart::create();
        $cart->save($cfixture->first());
	}

	public function testCartUpdate() {
    	$this->setUp();
		$post = array('cart' =>array(
		        '200001' => '4'
        ));
		$cartPuppet = new CartController();
		$cartPuppet->request->data = $post;
		$cartPuppet->update();
		$result = Cart::find('first', array('conditions' => array('_id' => '200001')));
		$this->assertEqual(4, $result->quantity, 'update was not successful');
		$this->tearDown();
	}

	public function tearDown() {
	    $efixture = Fixture::load('Event');
        $ifixture = Fixture::load('Item');
        $cfixture = Fixture::load('Cart');

        $event = Event::create();
        $event->remove($efixture->first());
        $item = Item::create();
        $item->remove($ifixture->first());
        $cart = Cart::create();
        $cart->remove($cfixture->first());

	}


}

?>
=======
use app\models\Cart;
use app\models\Item;
use app\controllers\CartController;
use MongoId;
use MongoDate;
use lithium\storage\Session;
use lithium\action\Request;

class CartControllerTest extends \lithium\test\Unit {
	
	/*
	* Testing the Remove method from the CartController
	*/
	public function testRemove() {
		//Configuration Test
		$cart_id = "787878787zazazag7878";
		$remote = new CartController(array('request' => new Request()));
		$remote->request->data = array('id'=>$cart_id);
		$remote->request->params['type'] = 'html';
		$user = Session::read('userLogin');
		$active_time  = new MongoDate();
		$expire_time  = new MongoDate();
		$expire_time->sec = ($expire_time->sec + (60*60*60)); 
		//Create temporary document
		$datas_cart = array(
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
		$cart->save($datas_cart);
		//Request the tested method
		$result = $remote->remove();
		//Test result
		$this->assertEqual( 0 , $result["cartcount"] );
	}

}
>>>>>>> e17c967948bb0b0d4cfdd0156c33c1aa4f854693
