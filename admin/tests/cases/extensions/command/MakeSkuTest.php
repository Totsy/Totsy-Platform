<?php

namespace admin\tests\cases\extensions\command;

use admin\extensions\command\MakeSku;
use admin\models\Item;
use MongoDate;
use MongoId;

class MakeSkuTest extends \lithium\test\Unit {
	public function testRun() {
		//configuration
		$item_A_id = new MongoId("4ddsqsdqszzz80f3323D3892614080076e0");
		$item_B_id = new MongoId("4ddsqsdqszzz80f232323232324080076e0");
		$event_id = "7878878UDUudsidisdisudiusU7";
		$remote = new MakeSku();
		$itemsCollection = Item::Collection();
		$result = 0;
		$datas_A = array(
			"_id" => $item_A_id,
			"color" => "Seymore",
			"details" => array(
				"no size" => 130
				),
			"event" => array(
				"0" => $event_id
			  ),
			"sku_details" => array(
				"no size" => "2R-4ED-B1C-B02"
			),
			"skus" => array(
				"0" => "2R-4ED-B1C-B02"
			),
			"vendor" => "2 red hens",
			"vendor_style" => "BB708",
		);
		$datas_B = array(
			"_id" => $item_B_id,
			"color" => "Seymore",
			"details" => array(
				"no size" => 130
				),
			"event" => array(
				"0" => $event_id
			  ),
			"vendor" => "2 red hens",
			"vendor_style" => "H708",
		);
		$items_A = Item::create();
		$items_A->save($datas_A);
		$items_B = Item::create();
		$items_B->save($datas_B);
		$remote->run();
		$items = $itemsCollection->find(array('event' => $event_id, 'skus' => array( '$in' => array("2R-4ED-B1C-B02"))));
		foreach($items as $item) {
			$result++;
		}
		$this->assertEqual( 1 , $result);
		Item::remove(array("_id" => $item_A_id));
		Item::remove(array("_id" => $item_B_id));
	}
}