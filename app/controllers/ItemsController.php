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
				$related = $item->related();
				$sizes = $item->sizes();
				$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}
		}

		return compact('item', 'event', 'related', 'sizes', 'shareurl');
	}

}

?>