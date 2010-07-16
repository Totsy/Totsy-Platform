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

	public function view($url = null) {
		$conditions = array('enabled' => 1) + compact('url');
		$item = Item::first(compact('conditions'));

		if (!$item) {
			// @todo: Handle error!
		}

		$event = $item->event();
		$related = $item->related();
		$sizes = $item->sizes();

		return compact('item', 'event', 'related', 'sizes');
	}

}

?>