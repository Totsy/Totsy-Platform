<?php

namespace app\controllers;
use app\models\Item;


/**
 * Handles the users main account information.
 */
class ItemsController extends \lithium\action\Controller {
	
	/**
	 * Main display of item data
	 */
	public function index() {
		$items = Item::find('all');		
		return compact('items');
	}
	/**
	 * Adds a product item to the database
	 */
	public function add() {
		//Check if there was a post request
		if ($this->request->data) {			
			$data = $this->request->data;
			//I know this looks ugly but until I fix the js thats the way its going to be
			$data = $data['itemDetails']['itemDetails'];
			//Create record	
			$Item = Item::create($data);
			//Save record
			$success = $Item->save($data);
			//Save the data of the record to display in form
			$itemData = $Item->data();
			if ($success) {
				$message = 'Item Successfully Added';
			}
		}
		return compact('message', 'itemData');
	}
	
	public function edit($id = null) {
		$item = Item::find('first', array('conditions' => array('_id' => $id)));
		if (empty($item)) {
			$this->redirect(array('controller' => 'items', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			$data = $this->request->data;
			$arrayData = $data['itemDetails']['itemDetails'];
			if ($item->save($arrayData)) {
				$this->redirect(array(
					'controller' => 'items', 'action' => 'edit',
					'args' => array($item->id)
				));
			}
		}
		return compact('item');
	}
}

?>