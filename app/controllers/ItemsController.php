<?php

namespace app\controllers;
use app\models\Navigation;
use app\models\Item;
use \lithium\storage\Session;


/**
 * Handles the users main account information.
 */
class ItemsController extends \lithium\action\Controller {
	
	/**
	 * Main display of item data
	 */
	public function index() {
		$htmlTable = $this->_buildItemTable();
		return compact('htmlTable');
	}

	/**
	 * Adds a product item to the database
	 */
	public function add() {
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
					'Color' => $data['Color'], 
					'Weight' => $data['Weight'], 
					'Size' => $data['Size'], 
					'Inventory' => $data['Inventory']
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
	
	/**
	 * Build the HTML item table.
	 * @return string
	 * @todo Make the item row linked to edit items.
	 * @todo Include quick buttons for delete
	 * @todo Include button link to associate media with items
	 */	
	private function _buildItemTable() {
		//Get the data
		
		$records = Item::find('all');
		
		$items = $records->data();
		if($items) {
			//Start clean
			$html = '';
			//Setup the table
			$html .= '<table id="itemTable" border="1" cellspacing="5" cellpadding="20" style="width: 1050px">';

			//We need the thead for jquery datatables
			$html .=  '<thead>'; 
			$html .= '<tr>';

			//Build the table headings first
			foreach ($items[0] as $key=>$value){
				//If we are on the attribute then get all the subitems
				if ($key == 'Attributes') {
					foreach ($value as $subKey=>$subValue) {
						//Build the table headings with subitems
						$html .= "<th>$subKey</th>";
					}
				} else {
					$html .=  "<th>$key</th>";
				}
			}
			//Set ending tags for html table headings
			$html .= '</tr></thead><tbody>';

			//Lets start building the data fields
			foreach ($items as $array) {
				//Let's first check if this array item has nested attributes
				if(isset($array['Attributes'][0])) {
					foreach ($array as $key => $value) {
						//Once we have an attribute lets build the whole row
						if ($key == 'Attributes') {
							$html .= '<tr>';
							//Now build out attribute data
							foreach ($value as $subarray) {
								//Build core item info each time we have a new attribute
								foreach ($array as $key => $value) {
									if ($key != 'Attributes') {
										$html .= '<td>'.$value.'</td>';
									}
								}
								//Build out the attribute fields
								foreach ($subarray as $attrKey => $attrVal) {
									$html .= "<td>$attrVal</td>";
								}
								$html .= '</tr>';
							}
						}	
					}		
				} else {
					$html .= '<tr>';
					//We dont have nested attributes here
					foreach ($array as $key => $value) {
						if ($key != 'Attributes') {
							$html .= '<td>'.$value.'</td>';
						} else {			
							foreach ($value as $attrKey => $attrVal) {
								$html .= "<td>$attrVal</td>";
							}
						}
					}
					$html .= '</tr>';
				}
			}
				$html .= "</tbody>";
				$html .= "</table>";
				return $html;
		} else {
			return $html = "There are no items";
		}
		
	}
}

?>