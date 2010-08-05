<?php

namespace admin\controllers;
use admin\controllers\BaseController;
use admin\models\Item;
use admin\models\Event;
use \MongoDate;


/**
 * Handles the users main account information.
 */
class ItemsController extends BaseController {
	
	/**
	 * Main display of item data
	 */
	public function index() {
		$created_date = 0;
		$modified_date = 0;
		$files = 0; 
		$items = Item::find('all', array(
			'fields' => compact(
				'created_date', 
				'modified_date', 
				'files'
		)));
		return compact('items');
	}
	/**
	 * Edits a product/item based on a preloaded CSV file.
	 * 
	 * The edit method has several parts that need to be parsed
	 * before saved to the database. This primarily applies to the
	 * images that are attached to the item. 
	 * @param string
	 * @return array
	 */
	public function edit($id = null) {
		$item = Item::find('first', array('conditions' => array('_id' => $id)));
		$event = Event::find('first', array('conditions' => array('_id' => $item->event[0])));

		if ($item) {
			$details = json_encode($item->details->data());
		} else {
			$this->redirect(array('controller' => 'items', 'action' => 'index'));
		}
		if ($this->request->data) {
			foreach ($this->request->data as $key => $value) {
				if (substr($key, 0, 10) == 'alternate-' ) {
					$alternate_images[] = substr($key, 10, 24);
					unset($this->request->data[$key]);
				}
			}
			if (!empty($item->event[0])) {
				$this->request->data['event'] = array($item->event[0]);
			}

			$dirtyUrl = $this->request->data['description']." ".$this->request->data['color'];
			$url = rtrim($this->cleanUrl($dirtyUrl), "-");
			$this->request->data['url'] = $url;

			$this->request->data['modified_date'] = new MongoDate();

			$data = array_merge(Item::castData($this->request->data), compact('alternate_images'));

			if ($item->save($data)) {
				$this->redirect(array(
					'controller' => 'items', 'action' => 'edit',
					'args' => array($item->_id)
				));
			}
		}
		return compact('item', 'details', 'event');
	}

	public function preview($url = null) {

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
		$this->_render['layout'] = 'preview';
		$id = $item->_id;
		$preview = 'Items';
		return compact('item', 'event', 'related', 'sizes', 'shareurl', 'id', 'preview');
	}
}

?>