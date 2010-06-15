<?php
namespace app\extensions\helper;


class Items extends \lithium\template\Helper {

	public function build($items = null) {
		if($items) {
			//Start clean
			$html = '';
			//Setup the table
			$html .= '<table id="itemTable" border="1" cellspacing="5" cellpadding="20" style="width: 1050px">';

			//We need the thead for jquery datatables
			$html .=  '<thead>'; 
			$html .= '<tr>';
		
			$heading = array(
				'_id', 
				'Name', 
				'Description', 
				'Original_Price', 
				'Sale_Price', 
				'Active', 
				'Vendor', 
				'Attributes' => array(
					'SKU', 
					'Color', 
					'Weight', 
					'Size', 
					'Inventory'
			));
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