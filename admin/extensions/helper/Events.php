<?php
namespace admin\extensions\helper;
use \MongoDate;

class Events extends \lithium\template\Helper {

	protected $_standardHeading = array(
		'name',
		'blurb',
		'start_date',
		'end_date',
		'enabled'
	);

	protected $_productHeading = array(
		'name',
		'start_date',
		'end_date',
		'Report'
	);

	protected $_creditHeading = array(
		'name',
		'start_date',
		'end_date',
		'Apply Credit'
	);
	protected $_emailHeading = array(
		'name',
		'start_date',
		'end_date',
		'Email Type'
	);

	protected $_links = array(
		'PO' => 'Reports::purchases',
		'Product File' => 'Reports::productfile',
		'Order File' => 'Reports::orders'
	);

	protected $_email = array(
		'General Emails' => 'Emails::send'
	);

	protected $_credit = array(
		'Apply Event Credit' => 'Credits::eventCredit'
	);

	public function build($eventRecords = null, $options = array('type' => null)){
		switch ($options['type']) {
			case 'logistics':
				$action = array('Reports::purchase');
				$heading = $this->_productHeading;
				break;
			case 'email':
				$action = array('Emails::new');
				$heading = $this->_emailHeading;
				break;
			case 'credits':
				$action = array('Credits::event');
				$heading = $this->_productHeading;
				break;
			default:
				$action = array('Events::edit');
				$heading = $this->_standardHeading;
				break;
		}
		if (!empty($eventRecords)) {
			$eventList = $eventRecords->data();
			$html = '';
			$html .= '<table id="itemTable" class="datatable" border="1">';
			$html .=  '<thead>';
			$html .= '<tr>';
			foreach ($heading as $value){
				$html .=  "<th>$value</th>";
			}
			$html .= '</tr></thead><tbody>';
			foreach ($eventList as $event) {
				$details = array_intersect_key($event, array_flip($heading));
				$orderedDetails = $this->sortArrayByArray($details, $heading);
				$link = array_merge($action, array('args' => $event['_id']));
				$html .= "<tr id=$event[_id]>";
				foreach ($orderedDetails as $key => $value) {
					if ($key == 'start_date' || $key == 'end_date') {
						$value = date('M-d-Y', $value);
					}
					if ($options['type'] == 'logistics') {
						$html .= "<td>$value</td>";
					} else {
						$html .= "<td>". $this->_context->html->link($value, $link, array('escape' => false))."</td>";
					}
				}
				if ($options['type'] == 'logistics') {
					$html .= "<td>";
					foreach ($this->_links as $name => $route) {
						$link = array($route,'args' => $event['_id']);
						$option = array('escape' => false);
						$html .= $this->_context->html->link("View - $name", $link, $option);
						$html .= "<br>";
					}
					$html .= "</td>";
				}
				if ($options['type'] == 'credits') {
					$html .= "<td>";
					foreach ($this->_credit as $name => $route) {
						$link = array($route, 'args' => $event['_id']);
						$option = array('escape' => false);
						$html .= $this->_context->html->link("$name", $link, $option);
						$html .= "<br>";
					}
					$html .= "</td>";
				}
				if ($options['type'] == 'email') {
					$html .= "<td>";
					foreach ($this->_email as $name => $route) {
						$link = array($route, 'args' => $event['_id']);
						$option = array('escape' => false);
						$html .= $this->_context->html->link("$name", $link, $option);
						$html .= "<br>";
					}
					$html .= "</td>";
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