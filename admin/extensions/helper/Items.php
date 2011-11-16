<?php
namespace admin\extensions\helper;
use admin\models\Item;

class Items extends \lithium\template\Helper {

	protected $heading = array(
		'Relate Items',
		'Images',
		'Description',
		'Copy',
		'Enabled'
	);

	public $current_item_id = "";

	//returns an array of all items in this event with the items's id as the key and the description + color as the value
	public function dropDownText($itemRecords){

		$items = array();

		//building an array of all items to be used in creating dropdowns
		foreach($itemRecords as $item) {
				if($item->color){
					$items["".$item->_id.""]['color'] = $item->color;
				} else {
					$items["".$item->_id.""]['color'] = "";
				}
			$items["".$item->_id.""]['description'] = $item->description;
		}

		return $items;
	}

	//writes out html dropdowns of items in an event, and selects the related items
	public function buildDropDown($all_items, $related_items = array()){

		$itemDropDown = "";

		if(!empty($related_items)){
			foreach( $all_items as $key=>$value ) {
				if($key!==$this->current_item_id){
				    $text = "";

				    if($value['color']){
				    	$text = $value['color']." - ".$value['description'];
				    } else {
				    	$text = $value['description'];
				    }

				    //if a related item is found
				    if(!in_array($key, $related_items) ) {
				    	$itemDropDown .= "<option value='".$key."' >" . $text . "</option>";
				    } else {
				    	$hasRelated = true;
				    	$itemDropDown .= "<option value='".$key."' disabled='1' selected='selected'>".$text."</option>";
				    }
				}
			}
		} else {
			foreach( $all_items as $key=>$value ) {
				if($key!==$this->current_item_id){
					if($value['color']) {
						$text = $value['color']." - ".$value['description'];
					} else {
						$text = $value['description'];
					}

					$itemDropDown .= "<option value='".$key."'>".$text."</option>";
				}
			}
		}

		return $itemDropDown;
	}

	public function build($itemRecords = null) {

		$html = "";
		$itemDropDown = "";

		//create blank array to check for duplicate color/description
		$itemUrlCheck = array();

		$all_items = Array();
		//set list of items with id as key and description + color as the value
		$all_items = $this->dropDownText($itemRecords);

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
			$itemslist = "nothing,";

			//Lets start building the data fields
			foreach ($itemRecords as $item) {
				$html .= "<tr class=''>";
				$html .= "<td width='400px'>";

				//make mini array of color/description
				$isurlduplicate = false;
				$urlcheckminiarray = array($item->url);

				if(in_array($urlcheckminiarray, $itemUrlCheck)){
					$isurlduplicate = true;
				}
				else{
					$itemUrlCheck[] = $urlcheckminiarray;
				}

				$this->current_item_id = "".$item->_id."";
				$itemslist .= $this->current_item_id . ",";

				$related_items = array();
				$itemDropDown = "";

				if(isset($item->related_items) && !empty($item->related_items)) {
					$related_items = $item->related_items->data();
				}

				$hasRelated = false;

				$html .= "<select multiple='multiple' id='related_".$item->_id."' class='related_items' name='related_".$item->_id."[]' title='Select an item'>";

				//$itemDropDown = $this->buildDropDown($all_items, $related_items);
				//$html .= $itemDropDown;
				$html .= "</select>";

				$html .= "</td>";

				$imagesHtml = $this->_images($item);
				$html .= "<td width='100'>{$imagesHtml}</td>";
				$html .= "<td width='200'><a href=\"/items/edit/$item->_id\">$item->description</a><br />
				Color: $item->color <br />
				Vendor Style: $item->vendor_style
				</td>";
				$html .= "<td height='100' width='100'><textarea rows='5' cols='20' name='$item->_id' id='$item->_id'>$item->blurb</textarea></td>";
				$html .= "<td width='30'>$item->enabled<br>";

				if($item->miss_christmas){
					$html .= "won't ship for xmas";
				}

				//check to show flag for duplicate color/description url
				if($isurlduplicate){
					$html .= "<br><span style='color:#ff0000;'>color and/or description are duplicated!</span>";
				}
				$html .= "</td>";
				$html .= '</tr>';

			}

			$itemslist = substr($itemslist, 0, -1);
			$html .= "</tbody>";
			$html .= "</table>";
			$html .= "
			<script>
			var allitemids = '$itemslist';
			</script>
			";
			return $html;
		} else {
			return $html = "There are no items";
		}
	}

	protected function _images($item=null) {
		if(is_object($item)) {
			$images = $item->images();

			$html = '<ul class="images sortable"';
			$html .= ' target="';
			$html .= $this->_context->url(array(
				'controller' => 'items', 'action' => 'orderImages',
				'item' => (string) $item->_id
			));
			$html .= '">';

			if (isset($images['primary'])) {
				$html .= '<li id="image-' . $images['primary']->_id . '">';
				$html .= $this->_context->html->image($images['primary']->url(), array(
					'alt' => 'item image'
				));
				$html .= '</li>';
			}
			if(isset($images['alternate'])) {
				foreach ($images['alternate'] as $image) {
					if(!empty($image)) {
						$html .= '<li id="image-' . $image->_id . '">';
						$html .= $this->_context->html->image($image->url(), array(
							'alt' => 'item image'
						));
						$html .= '</li>';
					}
				}
			}
			$html .= '</ul>';
			return $html;
		}
	}
}

?>