<?php

namespace app\controllers;
use app\models\Item;
use \MongoDate;


/**
 * Handles the users main account information.
 */
class ItemsController extends \lithium\action\Controller {
	
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
	 * Adds a product item to the database
	 */
	public function add() {
		//Check if there was a post request
		if ($this->request->data) {
			$itemData = $this->organizeItem($this->request->data);
			$itemData['created_date'] = new MongoDate();
			$itemData['files'] = $this->files($this->request->data);
			//Create record	
			$item = Item::create($itemData);
			//Save record
			$success = $item->save($itemData);
			if ($success) {
				$message = 'Item Successfully Added';
			}
		}
		return compact('message', 'item');
	}
	/**
	 * Edit an item
	 */
	public function edit($id = null) {
		$item = Item::find('first', array('conditions' => array('_id' => $id)));
		
		if ($item) {
			$details = json_encode($item->details->data());
		} else {
			$this->redirect(array('controller' => 'items', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			$itemData = $this->organizeItem($this->request->data);
			$itemData['modified_date'] = new MongoDate();
			$itemData['files'] = $this->files($this->request->data);
			if ($item->save($itemData)) {
				$this->redirect(array(
					'controller' => 'items', 'action' => 'edit',
					'args' => array($item->id)
				));
			}
		}
		return compact('item', 'details');
	}
	/**
	 * Reorganize the details of item data for document storage
	 */
	private function organizeItem($item) {
		$data = $item['itemDetails']['itemDetails'];
		$data['active'] = ($data['active'] == "Yes") ? 1 : 0;
		foreach ($data as $key => $value){
			if (is_numeric($key)) {
				$details["$key"] = $value;
			} else {
				$desc["$key"] = $value; 
			}
		}
		return array_merge($desc, array('details' => $details));
	}
	
	private function files($item)
	{
		unset($item['itemDetails']);
		return array_keys($item, 'on');
	}
	
}

?>