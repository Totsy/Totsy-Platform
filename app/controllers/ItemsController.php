<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Item;
use app\models\Event;
use app\models\Cart;

class ItemsController extends BaseController {

	protected function _init() {
		parent::_init();
		$this->_render['layout'] = 'main';
	}

	public function view($url = null) {

		if ($url == null) {
			$this->redirect('/');
		} else {
			$item = Item::find('first', array(
				'conditions' => array(
					'enabled' => true,
					'url' => $url),
				'order' => array('modified_date' => 'DESC'
			)));

			if (!$item) {
				$this->redirect('/');
			} else {
				$event = Event::find('first', array(
					'conditions' => array(
						'items' => array((string) $item->_id),
						'enabled' => true
				)));

				if ($event->end_date->sec < time()) {
					$this->redirect('/');
				} else {
					++$item->views;
					$item->save();
					$related = Item::related($item);
					$sizes = Item::sizes($item);
					$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				}
			}

		}

		return compact('item', 'event', 'related', 'sizes', 'shareurl', 'reserved');
	}

	public function available() {
		$available = false;
		$this->render(array('layout' => false));
		if ($this->request->query) {
			$data = $this->request->query;
			$reserved = Cart::reserved($data['item_id'], $data['item_size']);
			$item = Item::find('first', array(
				'conditions' => array(
					'_id' => $data['item_id']
			)));
			$size = ($data['item_size'] == 'undefined') ? 'no size' : $data['item_size'];
			$qty = $item->details->{$size} - $reserved;
			$available = ($qty > 0) ? true : false;
			echo json_encode($available);
		}
	}
}

?>