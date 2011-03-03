<?php

namespace app\tests\cases\controllers;

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