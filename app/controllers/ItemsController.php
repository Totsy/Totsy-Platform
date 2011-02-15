<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\models\Affiliate;
use app\models\Item;
use app\models\Event;
use app\models\Cart;

class ItemsController extends BaseController {

	protected function _init() {
		parent::_init();
		$this->_render['layout'] = 'main';
	}

	public function view() {
		$itemUrl = $this->request->item;
		$eventUrl = $this->request->event;
		if ($itemUrl == null || $eventUrl == null) {
			$this->redirect('/sales');
		} else {
			$event = Event::find('first', array(
				'conditions' => array(
					'enabled' => true,
					'url' => $eventUrl
			)));
			$items = Item::find('all', array(
				'conditions' => array(
					'enabled' => true,
					'url' => $itemUrl
			)));
			$matches = $items->data();
			foreach ($matches as $match) {
				if (in_array($match['_id'], $event->items->data())) {
					$item = Item::find($match['_id']);
				}
			}
			if ($item == null || $event == null) {
				$this->redirect('/sales');
			} else {
				if ($event->end_date->sec < time()) {
					$this->redirect('/sales');
				} else {
					++$item->views;
					$item->save();
					$related = Item::related($item);
					$sizes = Item::sizes($item);
					$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				}
			}
            $pixel = Affiliate::getPixels('product', 'spinback');
       // var_dump($item->primary_image);
			$spinback_fb = Affiliate::generatePixel('spinback', $pixel,
			                                            array('product' => $_SERVER['REQUEST_URI'])
			                                            );
		 //  die(var_dump($spinback_fb));
		}

		return compact('item', 'event', 'related', 'sizes', 'shareurl', 'reserved', 'spinback_fb');
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