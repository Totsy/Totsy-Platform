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

				//boolean for checking if a related item has already been selected
				$printed = false;

				//check if one of these items is a related item, and select it
				if(isset($item->related_items)) {
					//check if the related items field is an object
					if(is_object($item->related_items)) {

						//for counting the amount of related items
						$count = 1;

						//create dropdowns for related items
						foreach ($item->related_items as $ir) {
							$item_dropdown = "";

							foreach( $all_items as $key=>$value ) {
								$text = "";

								if($value['color']){
									$text = $value['color']." - ".$value['description'];
								} else {
									$text = $value['description'];
								}

								if($key == $ir){
									$item_dropdown .= "<option value='".$key."' selected='selected' class='related_item'>".$text."</option>";
								} else {
									$item_dropdown .= "<option value='".$key."' class='related_item'>".$text."</option>";
								}
							}

							$html .= "<select name='related".$count."_".$item->_id."' id='related".$count."_".$item->_id."'>";
							$html .= "<option value='' class='related_item'>Select an item</option>";

							$html .= $item_dropdown;
							$html .= "</select>";

							$count++;
						}

						//there could be a a maximum of 3 related items per item
						//if the # of related items is 2, add the 3rd dropdown
						if($count < 6) {

							for($i=0; $i<(6-$count);$i++){
								$item_dropdown = "";

								$inc = $i + $count;

								$html .= "<select name='related".$inc."_".$item->_id."' id='related".$inc."_".$item->_id."'>";
								$html .= "<option value='' selected='selected' size='".count($all_items)."' class='related_item'>Select an item</option>";

								foreach( $all_items as $key=>$value ) {
									$text = "";

									if($value['color']){
										$text = $value['color']." - ".$value['description'];
									} else {
										$text = $value['description'];
									}

									$item_dropdown .= "<option value='".$key."' class='related_item'>".$text."</option>";
								}

								$html .= $item_dropdown;
								$html .= "</select>";
							}
						}

					}
				} else {
					//create 3 dropdowns when there are no related items for this given item
					for ($i=1; $i<6; $i++) {
						$item_dropdown = "";

						$html .= "<select name='related".$i."_".$item->_id."' id='related".$i."_".$item->_id."'>";
						$html .= "<option value='' selected='selected' class='related_item'>Select an item</option>";

						//go through all items and select the option where all_items->_id matches $item->related_items
						foreach( $all_items as $key=>$value ) {

							if($value['color']) {
								$text = $value['color']." - ".$value['description'];
							} else {
								$text = $value['description'];
							}

							$item_dropdown .= "<option value='".$key."' class='related_item'>".$text."</option>";
						}

						$html .= $item_dropdown;
						$html .= "</select>";
					}
				}

				if (!empty($item->primary_image)) {
					$image = '/image/'. $item->primary_image . '.jpg';
				} else {
					$image = "/img/no-image-small.jpeg";
				}

				$html .= "</td>";
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