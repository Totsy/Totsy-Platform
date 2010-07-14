<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Item;
use app\models\Event;

class ItemsController extends BaseController {

	protected function _init() {
		parent::_init();
		$this->_render['layout'] = 'main';
	}

	public function view($url) {
		$item = Item::first(array(
			'conditions' => array(
				'enabled' => '1', 
				'url' => $url
		)));
		if (is_array($item->event) && $item->event) {
			$event = Event::first(reset($item->event));
		}
		return compact('item', 'event');
	}

}

?>