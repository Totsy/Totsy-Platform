<?php

namespace admin\models;
use MongoId;
use MongoDate;
use admin\extensions\util\String;

class Event extends \lithium\data\Model {

	public $validates = array();

	public static $tags = array(
		'holiday' => 'holiday',
		'special' => 'special',
		'toys' => 'toys'
	);

	/**
	 * Query for all the events within the next 24 hours.
	 *
	 * @return Object
	 */
	public static function open($params = null, array $options = array()) {
		$fields = $params['fields'];
		return Event::all(compact('fields') + array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array('$lte' => new MongoDate()),
				'end_date' => array('$gt' => new MongoDate())
			),
			'order' => array('start_date' => 'DESC')
		));
	}

	public static function collection() {
		return static::_connection()->connection->events;
	}

	protected $_booleans = array(
		'enabled'
		);

	public static function castData($event, array $options = array()) {

		foreach ($event as $key => $value) {
			if (in_array($key, static::_object()->_booleans)) {
				$event[$key] = (boolean) $value;
			}
		}
		return $event;
	}

	public static function removeItems($event) {
		return static::collection()->update(
			array('_id' => new MongoId($event)),
			array('$unset' => array('items' => true)
		));
	}

	/**
	 * Query for all events that are enabled and have a start date
	 * that is greater than "now".
	 *
	 * @return Object
	 */
	public static function pending() {
		return Event::all(array(
			'conditions' => array(
				'enabled' => true,
				'start_date' => array(
					'$gt' => new MongoDate())),
			'order' => array('start_date' => 'ASC')
		));
	}

	public static function poNumber($event) {
		$vendorName = preg_replace('/[^(\x20-\x7F)]*/','', substr(String::asciiClean($event->name), 0, 3));
		$time = date('ymdis', $event->_id->getTimestamp());
		return 'TOT'.'-'.$vendorName.$time;
	}
	
	
	public static function makecell($content, $error = false) {
		if($error){
			return "<div class=xls_cell_error>$content</div>";
		}
		else{
			return "<div class=xls_cell>$content</div>";
		}
	}
	
	
	
	/**
	 * Takes copy/pasted XLS content and converts to multi-dimensional array
	 * @var returns array
	 */
	public static function convert_spreadsheet($val){
		$fullarray = array();
	
		$rows = explode("\n", $val); 
		
		foreach($rows as $thisrows){
			$fields = explode("\t", $thisrows); 
			$fullarray[] = $fields;
		}
		
		return $fullarray;
	}
	
	
	
	public static function check_spreadsheet($array){
	
		//arrays for datatypes to check uniqueness
		$output = "";
		$check_vendor_style = array();
		
		//arrays of header names to check stuff
		$check_required = array("vendor", "vendor_style", "category", "sub-category", "description", "quantity");
		$check_badchars = array("vendor", "vendor_style", "age", "category", "sub-category", "description", "color", "no size");
		$check_decimals = array("msrp", "sale_retail", "percentage_off", "orig_wholesale", "sale_wholesale", "imu");
		$check_departments = array("Girls", "Boys", "Momsdads");
		$check_dept = array("department_1", "department_2", "department_3");
		$check_related = array("related_1", "related_2", "related_3", "related_4", "related_5");
	
		$highestRow = $array[0];
		$totalrows = count($array);
		$totalcols = count($highestRow);

		for ($row = 0; $row <= $totalrows-1; ++ $row ) {
			for ($col = 0; $col < $totalcols; ++ $col) {
				$val = $array[$row][$col];
				
				if ($row == 0) {
					$output .= Event::makecell($val);
					$heading[] = $val;
				} 
				else {
					if (isset($heading[$col])) {

						//checking formulas in each row, checks to see if = is first char
						if(substr($val, 0, 1)=="="){
							$errors[] = "$heading[$col] has a formula in row #$row";
							$output .= Event::makecell($val, true);
						}
					
						//check required fields
						if (in_array($heading[$col], $check_required)) {
							if (empty($val)) {
								$errors[] = "$heading[$col] (required) is blank for row #$row";
								$output .= Event::makecell($val, true);
							}
						}
						
						//check for bad chars	
						if (in_array($heading[$col], $check_badchars)) {
					
							if (!empty($val)) {
								if(strpos($val, "&")){
									if(!strpos($val, "\&")){
										$errors[] = "$heading[$col] has an illegal character in row #$row";
										$output .= Event::makecell($val, true);
									}
								}
								if(strpos($val, "!")){
									if(!strpos($val, "\!")){
										$errors[] = "$heading[$col] has an illegal character in row #$row";
										$output .= Event::makecell($val, true);
									}
								}
							}
						}
					
						if (in_array($heading[$col], $check_dept)) {
							if (!empty($val)) {	
								$eventItems[$row - 1]['departments'][] = ucfirst(strtolower(trim($val)));
								$eventItems[$row - 1]['departments'] = array_unique($eventItems[$row - 1]['departments']);
	
								if (!in_array($val, $check_departments)) {
									$errors[] = "$heading[$col] is incorrect in row #$row";
									$output .= Event::makecell($val, true);
								}
								else{
									$output .= Event::makecell($val);
								}
	
							}
						} elseif (in_array($heading[$col], $check_related)) {
								if (!empty($val)) {
									$eventItems[$row - 1]['related_items'][] = trim($val);
									$eventItems[$row - 1]['related_items'] = array_unique($eventItems[$row - 1]['related_items']);
									$output .= Event::makecell($val);
								}
						//check if vendor style is unique
						} elseif ($heading[$col] === "vendor_style"){
							if (empty($val)) {
								$errors[] = "$heading[$col] is blank for row #$row";
								$output .= Event::makecell($val, true);
							}
							if(in_array(trim($val), $check_vendor_style)){
								//check if color/description is unique
							
							
								$errors[] = "$heading[$col] is a duplicate in row #$row";
								$output .= Event::makecell($val, true);
							}else{
								$check_vendor_style[] = trim($val);
								$output .= Event::makecell($val);
							}
						
						//check decimals here
						} elseif (in_array($heading[$col], $check_decimals)) {
							if (!empty($val)) {
								if(is_numeric($val)){
									$val = number_format($val, 2, '.', '');
								}
							}
							$output .= Event::makecell($val);
	
						} else {
							$output .= Event::makecell($val);
							if (!empty($val)) {
								$eventItems[$row - 1][$heading[$col]] = $val;
							}
						}

					
					}
				}
			}
			$output .= "<div style=\"clear:both;\"></div>";
		}
	
		//$errors[] = "you dont even know what youre doing!!!";
		if(count($errors)>0){
			$error_output="<h3>Spreadsheet Errors:</h3>";
			foreach($errors as $thiserror){
				$error_output .= $thiserror . "<br>";
			}
		}
			
		if($error_output){
			return "<div class=xls_holder_inner>$output</div>";
		}
		else{
			return "success";
		}
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>