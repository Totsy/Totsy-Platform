<?php
namespace admin\extensions\helper;
use \MongoDate;

class Banners extends \lithium\template\Helper {

	protected $_standardHeading = array(
		'name',
		'end_date',
		'enabled'
	);

	public function build($bannerRecords = null){
		$action = array('Banners::edit');
		$heading = $this->_standardHeading;
		if (!empty($bannerRecords)) {
			$bannerList = $bannerRecords->data();
			$html = '';
			$html .= '<table id="itemTable" class="datatable" border="1">';
			$html .=  '<thead>';
			$html .= '<tr>';
			foreach ($heading as $value){
				$html .=  "<th>$value</th>";
			}
			$html .= '</tr></thead><tbody>';
			foreach ($bannerList as $banner) {
				$details = array_intersect_key($banner, array_flip($heading));
				$orderedDetails = $this->sortArrayByArray($details, $heading);
				$link = array_merge($action, array('args' => $banner['_id']));
				$html .= "<tr id=$banner[_id]>";
				foreach ($orderedDetails as $key => $value) {
					if ($key == 'end_date') {
						$value = date('M-d-Y', $value);
					}
					$html .= "<td>". $this->_context->html->link($value, $link, array('escape' => false))."</td>";
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