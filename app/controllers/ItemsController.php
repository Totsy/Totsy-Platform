<?php

namespace app\controllers;

use app\models\Item;
use app\models\Event;

class ItemsController extends \lithium\action\Controller {

	protected function _init() {
		parent::_init();
		$this->_render['layout'] = 'product';
	}

	public function view() {
		$item = Item::first($this->request->id);
		$event = null;

		if (is_array($item->event) && $item->event) {
			$event = Event::first(reset($item->event));
		}
		return compact('item', 'event');
	}

	public function add() {
		$item = Item::create();

		if (($this->request->data) && $item->save($this->request->data)) {
			$this->redirect(array('Items::view', 'id' => $item->_id));
		}
		return compact('item');
	}

	public function edit() {
		$item = Item::find($this->request->id);

		if (!$item) {
			$this->redirect('Items::index');
		}
		if (($this->request->data) && $item->save($this->request->data)) {
			$this->redirect(array('Items::view', 'id' => $item->_id));
		}
		return compact('item');
	}
}

?>