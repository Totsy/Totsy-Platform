<?php
namespace admin\extensions\helper;
use admin\models\Item;

class Items extends \lithium\template\Helper {

	protected $heading = array(
		'Relate Items',
		'Primary Image',
		'Description',
		'Copy',
		'Enabled'
	);

	public function build($itemRecords = null) {

		$html = "";
		$all_items = array();

		//building an array of all items to be used in creating dropdowns
		foreach($itemRecords as $item) {

			if($item->color){
				$all_items["".$item->_id.""]['color'] = $item->color;
			} else {
				$all_items["".$item->_id.""]['color'] = "";
			}

			$all_items["".$item->_id.""]['description'] = $item->description;
		}

		if (!empty($itemRecords)) {
			$html .= "<table id='itemtable'";
			//We need the thead for jquery datatables
			$html .=  '<thead>';
			$html .= '<tr>';

			//Build the table headings first
			foreach ($this->heading as $key) {
				$html .=  "<th>$key</th>";
			}

			$i = 0;
			$image = "";

			//Set ending tags for html table headings
			$html .= '</tr></thead><tbody>';

			//Lets start building the data fields
			foreach ($itemRecords as $item) {
				$html .= "<tr class=''>";
				$html .= "<td width='400px'>";
				
				$itemDropdown = "";
				$hasRelated = false;

				$html .= "<select multiple='multiple' id='related_".$item->_id."' name='related_".$item->_id."[]' title='Select an item'>";

				//check if one of these items is a related item, and select it
				if(isset($item->related_items)) {
					//go through all event items
					foreach( $all_items as $key=>$value ) {
						$text = "";

						if($value['color']){
							$text = $value['color']." - ".$value['description'];
						} else {
							$text = $value['description'];
						}
						
						//if a related item is found
						if(!in_array($key, $item->related_items->data())) {
							$itemDropdown .= "<option value='".$key."' >" . $text . "</option>";
						} else {
							$hasRelated = true;
							$itemDropdown .= "<option value='".$key."' disabled='1' selected='selected'>".$text."</option>";
						}
					}
					
				} else {
					
					foreach( $all_items as $key=>$value ) {

						if($value['color']) {
							$text = $value['color']." - ".$value['description'];
						} else {
							$text = $value['description'];
						}

						$itemDropdown .= "<option value='".$key."'>".$text."</option>";
					}

					
				}
				
				$html .= $itemDropdown;
				$html .= "</select>";
				
				$html .= "</td>";

				if (!empty($item->primary_image)) {
					$image = '/image/'. $item->primary_image . '.jpg';
				} else {
					$image = "/img/no-image-small.jpeg";
				}
				
				$html .= "<td width='100'><img src=$image/ width='75'></td>";
				$html .= "<td width='200'><a href=\"/items/edit/$item->_id\">$item->description</a><br />
				Color: $item->color <br />
				Vendor Style: $item->vendor_style
				</td>";
				$html .= "<td height='100' width='100'><textarea rows='5' cols='20' name='$item->_id' id='$item->_id'>$item->blurb</textarea></td>";
				$html .= "<td width='30'>$item->enabled</td>";
				$html .= '</tr>';

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