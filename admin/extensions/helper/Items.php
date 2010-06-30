<?php
namespace admin\extensions\helper;


class Items extends \lithium\template\Helper {
	
	protected $heading = array( 
		'vendor',
		'vendor_style',
		'description',
		'active'
	);
	protected $table = array(
		'itemTable',
		'datatable'
	);

	public function build($itemRecords = null) {
		$html = '';
		if(!empty($itemRecords)) {
			$itemData = $itemRecords->data();
			//Setup the table
			list($id, $class) = $this->table;
			$html .= "<table id=\"$id\" class=\"$class\">";
			//We need the thead for jquery datatables
			$html .=  '<thead>'; 
			$html .= '<tr>';

			//Build the table headings first
			foreach ($this->heading as $key){
				$html .=  "<th>$key</th>";
			}
			//Set ending tags for html table headings
			$html .= '</tr></thead><tbody>';
			//Lets start building the data fields
			foreach ($itemData as $item) {
				$details = array_intersect_key($item, array_flip($this->heading));
				$ordered = $this->sortArrayByArray($details, $this->heading);
				$ordered['active'] = ($ordered['active'] == 1) ? 'Yes' : 'No';
				$link = "href=\"/items/edit/$item[_id]\"";
				foreach ($ordered as $key => $value) {
					if ($key == 'description') {
						$html .= "<td><a $link>$value</a></td>";
					} else {
						$html .= "<td>$value</td>";
					}
				}
				$html .= '</tr>';
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