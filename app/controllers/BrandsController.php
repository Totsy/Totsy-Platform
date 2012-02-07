<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Event;
use app\models\Item;
use app\models\Banner;
use MongoDate;
use lithium\storage\Session;
use app\models\Affiliate;


class BrandsController extends BaseController {

	public function index() {
		
		echo "temp";
		$this->_render['template'] = 'index';
		return;
	}

	public function view() {
		$datas = $this->request->args;
		
		$openEventsData = Event::open()->data();
		$openEvents = array_slice($openEventsData,0,$this->showEvents,true);
		
	
			//if($this->request->is('mobile')){
		// 	$this->_render['layout'] = 'mobile_main';
		// 	$this->_render['template'] = 'mobile_age';
	//	}
		return compact('openEvents');
	}

}



?>
