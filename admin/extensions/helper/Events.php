<?php
namespace admin\extensions\helper;
use \MongoDate;

class Events extends \lithium\template\Helper {

	public function build($eventRecords = null) {
		if (!empty($eventRecords)) {
			$eventList = $eventRecords->data();

			$html = '';

			$heading = array(
				'name', 
				'blurb', 
				'start_date', 
				'end_date', 
				'enabled'
			);
			$html .= '<table id="itemTable" class="datatable" border="1" style="width: 500px">';
			$html .=  '<thead>'; 
			$html .= '<tr>';
			//Build the table headings first
			foreach ($heading as $value){
				$html .=  "<th>$value</th>";
			}
			//Set ending tags for html table headings
			$html .= '</tr></thead><tbody>';
			//Lets start building the data fields
			foreach ($eventList as $event) {
				$details = array_intersect_key($event, array_flip($heading));
				$orderedDetails = $this->sortArrayByArray($details, $heading);
				$link = "href=\"/events/edit/$event[_id]\"";
				$html .= "<tr id=$event[_id]>";
				foreach ($orderedDetails as $key => $value) {
					if ($key == 'start_date' || $key == 'end_date') {
						$value = date('M-d-Y', $value['sec']);
					}
					$html .= "<td><a $link>$value</a></td>";
				}
				$html .= '</tr>';
			}
		}
		return $html;
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