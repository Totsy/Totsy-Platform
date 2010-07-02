<?php

namespace admin\controllers;
use admin\models\Item;
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
		$primaryImages = array();
		$secondaryImages = array();
		if ($item) {
			$details = json_encode($item->details->data());
		} else {
			$this->redirect(array('controller' => 'items', 'action' => 'index'));
		}
		if (!empty($this->request->data)) {
			
			$itemData = $this->organizeItem($this->request->data);
			foreach ($this->request->data as $key => $value) {
				if (substr($key, 0, 8) == 'primary-' ) {
					$primaryImages[] = substr($key, 8, 24);
				}
				if (substr($key, 0, 10) == 'secondary-' ) {
					$secondaryImages[] = substr($key, 10, 24);
				}
			}
			foreach ($itemData as $key => $value) {
				if (substr($key, 0, 8) == 'primary-' ) {
					$primaryImages[] = $value;
					unset($itemData[$key]);
				}
				if (substr($key, 0, 10) == 'secondary-' ) {
					$secondaryImages[] = $value;
					unset($itemData[$key]);
				}
			}
			$itemData['modified_date'] = new MongoDate();
			$itemData = array_merge($itemData, array(
				'primary_images' => $primaryImages), 
				array('secondary_images' => $secondaryImages
			));
			if ($item->save($itemData)) {
				$this->redirect(array(
					'controller' => 'items', 'action' => 'edit',
					'args' => array($item->_id)
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
		$data['enabled'] = ($data['enabled'] == "Yes") ? 1 : 0;
		foreach ($data as $key => $value){
			if (is_numeric($key)) {
				$details["$key"] = $value;
			} else {
				$desc["$key"] = $value; 
			}
		}
		return array_merge($desc, array('details' => $details[0]));
	}
	
	public function view($params, array $options = array()) {}
}

?>