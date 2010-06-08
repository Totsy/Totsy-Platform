<?php

namespace app\controllers;
use app\models\Navigation;
use app\models\Item;
use \lithium\storage\Session;


/**
 * Handles the users main account information.
 */
class ItemsController extends \lithium\action\Controller {
	
	public function index() {
		$items = Item::find('all')->data();
		return compact('items');
	}

	/**
	 * Adds a product item to the database
	 */
	public function add()
	{
		//Check if there was a post request
		if ($this->request->data) {
			//Let's put this in another var
			$data = $this->request->data;
			//Determine if we have more than one item by checking if the SKU is an array
			if (is_array($data['SKU'])){
				
				//Grab all the item property arrays
				$sku = $data['SKU'];
				$color = $data['Color'];
				$weight = $data['Weight'];
				$size = $data['Size'];
				$inventory = $data['Inventory'];
				//Initialize our $item holder
				$item = array();
				//Setup counter
				$itemCount = count($sku) - 1;

				$i=0;
				//Loop through each item and reorganize and group attribute information
				while ($i <= $itemCount) {
					$items[] = array(
						'SKU' => "$sku[$i]",
						'Color' => "$color[$i]",
						'Weight' => "$weight[$i]",
						'Size' => "$size[$i]",
						'Inventory' => "$inventory[$i]"
						);
					$i++;
				}
			} else {
				//If we don't have an array lets make one out of the one item
				$items = array(
					'SKU' => $data['SKU'], 
					'Color' => $data['SKU'], 
					'Weight' => $data['SKU'], 
					'Size' => $data['SKU'], 
					'Inventory' => $data['SKU']
				);
			}
			
			//Unset original values
			unset($data['SKU'], $data['Color'], $data['Weight'], $data['Size'], $data['Inventory']);
			//Merge cleaned up array and new item array
			$request = array_merge($data, array('Attributes' => $items));
			//Create record	
			$Item = Item::create($request);
			//Save record
			$success = $Item->save($request);
			//Save the data of the record to display in form
			$itemData = $Item->data();
			if ($success) {
				$message = 'Item Successfully Added';
			}
		}
		return compact('message', 'itemData');
	}
}
?>