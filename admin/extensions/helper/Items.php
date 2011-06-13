<?php
namespace admin\extensions\helper;


class Items extends \lithium\template\Helper {
	
	protected $heading = array(
		'Primary Image',
		'Description',
		'Copy',
		'Enabled'
	);

	public function build($itemRecords = null) {
		$html = '';
		if (!empty($itemRecords)) {
			$html .= "<table id='itemtable'";
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
			foreach ($itemRecords as $item) {
				$html .= '<tr>';
				if (!empty($item["primary_image"])) {
					$image = '/image/'. $item["primary_image"] . '.jpg';
				} else {
					$image = "/img/no-image-small.jpeg";
				}
				$html .= "<td width='100'><img src=$image/ width='75'></td>";
				$html .= "<td width='200'><a href=\"/items/edit/$item[_id]\">$item[description]</a>
				<br />
					Color: $item[color] <br />
					Vendor Style: $item[vendor_style]
				</td>";
				$html .= "<td><textarea name='$item[_id]' id='$item[_id]'>$item[blurb]</textarea></td>";
				$html .= "<td>$item[enabled]</td>";
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