<?php
namespace admin\extensions\helper;


class Items extends \lithium\template\Helper {

	public function build($itemRecords = null) {
		if(!empty($itemRecords)) {
			$items = $itemRecords->data();
			//Start clean
			$html = '';
			//Setup the table
			$html .= '<table id="itemTable" class="datatable" border="1" cellspacing="5" cellpadding="20" style="width: 1050px">';
			//We need the thead for jquery datatables
			$html .=  '<thead>'; 
			$html .= '<tr>';
			$heading = array(
				'_id', 
				'name', 
				'description', 
				'original_price', 
				'sale_price', 
				'active', 
				'vendor',
				'sku', 
				'color', 
				'weight', 
				'size', 
				'inventory'
			);
			//Build the table headings first
			foreach ($heading as $key){
				//If we are on the attribute then get all the subitems
				if (is_array($key)) {
					foreach ($key as $subKey) {
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
				$array = $this->sortArrayByArray($array, $heading);
				$array['active'] = ($array['active'] == 1) ? 'Yes' : 'No';
				$link = "href=\"/items/edit/$array[_id]\"";
				//Let's first check if this array item has nested Details
				if(isset($array['details'][0])) {
					foreach ($array as $key => $value) {
						//Once we have an attribute lets build the whole row
						if ($key == 'details') {
							//Now build out attribute data
							foreach ($value as $subarray) {
								//Build core item info each time we have a new attribute
								$html .= "<tr id=$array[_id]>";
								foreach ($array as $key => $value) {
									if ($key != 'details') {
										if ($key == 'name' || $key == '_id') {
											$html .= "<td><a $link>$value</a></td>";
										} else {
											$html .= "<td>$value</td>";
										}
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
					$html .= "<tr id=$array[_id]>";
					//We dont have nested Details here
					foreach ($array as $key => $value) {
						if ($key != 'details') {
							if ($key == 'name' || $key == '_id') {
								$html .= "<td><a $link>$value</a></td>";
							} else {
								$html .= "<td>$value</td>";
							}
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
	
	public function sortArrayByArray($array,$orderArray) {
		$ordered = array();
		foreach($orderArray as $key => $value) {
			if(array_key_exists($value, $array)) {
				$ordered[$value] = $array[$value];
				unset($array[$value]);
			}
		}
	    return $ordered + $array;
	}
}

?>