<?php

namespace admin\controllers;
use admin\controllers\BaseController;
use admin\models\Item;
use admin\models\Event;
use MongoRegex;
use MongoDate;
use MongoId;
use Mongo;
use \li3_flash_message\extensions\storage\FlashMessage;

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
		#T Get all possibles value for the multiple filters select
		$sel_filters = array();
		$all_filters = array();
		$result =  Item::getDepartments();
		foreach ($result['values'] as $value) {
			$all_filters[$value] = $value;
			if (array_key_exists('Momsdads',$all_filters) && !empty($all_filters['Momsdads'])) {
				$all_filters['Momsdads'] = 'Moms & Dads';
			}
		}
		#T Get selected values of filters

		if(!empty($item->departments)) {
			$values = $item->departments->data();
			foreach ($values as $value) {
				$sel_filters[$value] = $value;
			}
		}
		#END T
		if ($this->request->data) {
			$alternate_images = array();
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
			$this->request->data['url'] = $this->cleanUrl($dirtyUrl);
			$this->request->data['modified_date'] = new MongoDate();
			$data = array_merge(Item::castData($this->request->data), compact('alternate_images'));
			//Clean filters posts
			if(!empty($data["departments"])) {
				foreach($data["departments"] as $value) {
					if(!empty($value)) {
						$departments[] = ucfirst($value);
					}
				}
				$data["departments"] = $departments;
			}
			if ($item->save($data)) {
				$this->redirect(array(
						'controller' => 'items', 'action' => 'edit',
						'args' => array($item->_id)
					));
			}
		}
		return compact('item', 'details', 'event', 'all_filters', 'sel_filters');
	}

	public function preview() {
		$itemUrl = $this->request->item;
		$eventUrl = $this->request->event;
		if ($itemUrl == null || $eventUrl == null) {
			$this->redirect('/');
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
				$this->redirect('/');
			} else {
				$event = Event::find('first', array(
						'conditions' => array(
							'items' => array((string) $item->_id)
						)));
				$related = Item::related($item);
				$sizes = Item::sizes($item);
				$shareurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}
		}
		$this->_render['layout'] = 'preview';
		$id = $item->_id;
		$preview = 'Items';
		return compact('item', 'event', 'related', 'sizes', 'shareurl', 'id', 'preview');
	}

	/**
	 * Remove Items from Event and Item Collection
	 *
	 * Based on the event _id items will be removed from the Item collection.
	 * The item field will also be unset.
	 * @return array
	 */
	public function removeItems() {
		if ($this->request->data) {
			$id = $this->request->data['event'];
			$event = Event::find('first', array('conditions' => array('_id' => $id)));
			if ($event->views <= 0){
				if ((!empty($event->items)) && Item::remove(array('event' => $id)) && Event::removeItems($id)) {
					FlashMessage::set('Items Removed', array('class' => 'pass'));
				} else {
					FlashMessage::set('Remove Failed', array('class' => 'warning'));
				}
			} else {
				FlashMessage::set('Items Cannot Be Removed the Event is Live', array('class' => 'fail'));
			}
			$this->redirect(array('Events::edit','args' => array($id)));
		}
	}

	public function search() {
		if ($this->request->data) {
			$search = $this->request->data['search'];
			$itemCollection = Item::connection()->connection->items;
			$items = $itemCollection->find(
				array('$or' => array(
						array('description' => new MongoRegex("/$search/i")),
						array('vendor' => new MongoRegex("/$search/i")),
						array('vendor_style' => new MongoRegex("/$search/i")),
						array('skus' => array('$in' => array(new MongoRegex("/$search/i"))))
					)));
		}
		return compact('items');
	}

	/**
	 * Update Items from Items Collection
	 * Based on the event _id items will be update from the Item collection.
	 */
	public function itemUpdate() {
		$itemsCollection = Item::Collection();
		$itemId = array();

		if ($this->request->data) {
			$data = $this->request->data;
			$id = $data['id'];
			unset($data['id']);
			array_reverse($data);
			
			foreach ($data as $key => $value) {
				//check if this is the related items (dropdown selection) or the description (text area)
				if(substr_count($key, '_')==0) {
					$itemId = array("_id" => new MongoId($key));
					//setting the copy
					if($value) {
						$itemsCollection->update($itemId, array('$set' => array("blurb" => $value)));
					}
				} else {
					//setting the related item
					if($value){
						//parse out the related#_ portion from the string
						$itemsCollection->update(array("_id" => new MongoId(substr($key, (strrpos($key, "_") + 1)))), array('$addToSet' => array('related_items' => $value)));
					}
				}
			}

			$this->redirect('/events/edit/'.$id.'#event_items');

		}
	}
}

?>