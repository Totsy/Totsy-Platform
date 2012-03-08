<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Event;
use app\models\Item;
use app\models\Banner;
use MongoId;
use MongoDate;
use lithium\storage\Session;
use app\models\Affiliate;


class BrandsController extends BaseController {

	public function index() {
		
		echo "temp";
		$this->_render['template'] = 'index';
		return;
	}

	public function view($id) {
		$datas = $this->request->args;
		
		//hardcoded array of event ids for jojo
		$eventids[] = new MongoId("4f4d69071d5ecb2653000039");
		$eventids[] = new MongoId("4f4e3b5a1d5ecbd179000010");
		$eventids[] = new MongoId("4f4e3e2f1d5ecb6079000052");
		$eventids[] = new MongoId("4f4e829d1d5ecb790a00008d");
		$eventids[] = new MongoId("4f4e4d121d5ecbe47d000019");
		$eventids[] = new MongoId("4f4e47b81d5ecb047c000061");	

		//query of these six events
		$openEvents = Event::find('all', array('conditions' => array('_id' => array('$in' => $eventids))));
		
		$registerlink = "/register";
		
		if($id == "jojo1"){
			$registerlink = "/a/facebookjojo";
		}
		
		return compact('openEvents','registerlink');
	}

}



?>
