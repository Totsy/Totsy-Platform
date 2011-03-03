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
        var_dump($efixture->toArray());
        die();
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
		$result = Cart::find('first', array('conditions' => array('_id' => $post['_id'])));
		$this->assertEqual($post['qty'], $result->quantity, 'update was not successful');
	}

	public function tearDown() {}


}

?>