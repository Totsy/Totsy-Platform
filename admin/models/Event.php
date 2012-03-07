<?php

namespace admin\models;
use MongoId;
use MongoDate;
use admin\models\Item;
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
		'enabled',
		'viewlive',
		'clearance',
		'tangible'
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
		$className = 'xls_cell';

		if ($error) {
			$className .= ' error';
		}

		return sprintf('<div class="%s">%s</div>', $className, $content);
	}

	/**
	 * Convert text data in tabular format into an array.
	 *
	 * @param $val The text data in tabular format.
	 * @var returns array
	 */
	public static function convert_spreadsheet($val){
		$fullarray = array();

		$rows = explode(PHP_EOL, $val);

		foreach ($rows as $thisrows) {
			$fields = explode("\t", $thisrows);

			if (!empty($fields[0])
				|| !empty($fields[1])
				|| !empty($fields[2])
				|| !empty($fields[3])
				|| !empty($fields[4])
			) {
				$fullarray[] = $fields;
			}
		}

		return $fullarray;
	}

	public static function convert_smart_quotes($string){
	    $search = array(chr(145),
	                    chr(146),
	                    chr(147),
	                    chr(148),
	                    chr(151));

	    $replace = array('"',
	    				 '"',
	    				 "'",
	                     "'",
	                     '"',
	                     '"',
	                     '-');

	    return str_replace($search, $replace, $string);
	}

	/**
	 * Validate a grid of event items.
	 *
	 * @param $array The grid of event items, in array format
	 * @param $mapCategories A map of category and age identifiers to names.
	 * @return An HTML fragment containing the original data and possibly a list
	 *	of errors.
	 */
	public static function check_spreadsheet(array $array, $mapCategories){

		//arrays for datatypes to check uniqueness
		$output = "";
		$check_vendor_style = array();
		$heading = array();

		//arrays of header names to check stuff
		$check_required = array("vendor", "vendor_style", "description", "quantity");
		$check_badchars = array("vendor", "vendor_style", "age", "category", "sub-category", "color", "no size");
		$check_decimals = array("msrp", "sale_retail", "percentage_off", "percent_off", "orig_wholesale", "orig_whol", "sale_whol", "sale_wholesale", "imu");
		$check_departments = array("Girls", "Boys", "Momsdads");
		$check_dept = array("department_1", "department_2", "department_3");
		$check_related = array("related_1", "related_2", "related_3", "related_4", "related_5");
		$check_alphanumeric = array('vendor', 'vendor_style');
		$check_limit = array(
			'description' => 64
		);

		$highestRow = $array[0];
		$totalrows = count($array);
		$totalcols = count($highestRow);
		$field_size_start = 0;
		$field_size_end = 0;

		for ($row = 0; $row <= $totalrows-1; ++ $row) {
			$size_quantity = 0;	
			$output .= Event::makecell($row+1);
			for ($col = 0; $col < $totalcols; ++ $col) {
				$val = $array[$row][$col];
				$val = trim($val);
				$thiserror = "";

				if ($row == 0) {
					$heading[] = $val;
					if(strpos($val, "1/2")){
						$thiserror = "header error - $heading[$col] is illegal - has a half";
					}

					if ('color' == $val) {
						$field_size_start = $col;
					} else if ('total_quantity' == $val) {
						$field_size_end = $col;
					}
				} else {
					if (isset($heading[$col]) && (in_array($heading[$col], $check_required))&&(empty($val))) {
						$thiserror = "$heading[$col] (required) is blank for row #$row";
					} else {
						if((strstr($heading[$col], "category_"))&&(!empty($val))) {
							if(!in_array($val, $mapCategories['category'])){
								$thiserror = "$heading[$col] has an illegal category in row #$row";
							}
						}
						if((strstr($heading[$col], "age_"))&&(!empty($val))) {
							if(!in_array($val, $mapCategories['age'])){
								$thiserror = "$heading[$col] has an illegal age in row #$row";
							}
						}
						if (isset($heading[$col]) &&
							in_array($heading[$col], $check_alphanumeric) &&
							!preg_match('/^[a-zA-Z0-9]{3}\w*/i', $val)
						) {
							$thiserror = "$heading[$col] is invalid. The first three characters must be alphanumeric (letters or digits only).";
						}
						if (isset($heading[$col]) &&
							array_key_exists($heading[$col], $check_limit) &&
							strlen($val) > $check_limit[$heading[$col]]
						) {
							$thiserror = "$heading[$col] is too long at " . strlen($val) . " characters. It must not exceed " . $check_limit[$heading[$col]] . " characters.";
						}
						/*
						if ((in_array($heading[$col], $check_badchars))&&(!empty($val))) {
							if((strpos($val, "&"))&&(!strpos($val, "\&"))){
								$thiserror = "$heading[$col] has an illegal character in row #$row";
							}
							if((strpos($val, "!"))&&(!strpos($val, "\!"))){
								$thiserror = "$heading[$col] has an illegal character in row #$row";
							}
							if((strpos($val, "'"))&&(!strpos($val, "\'"))){
								$thiserror = "$heading[$col] has an illegal character in row #$row";
							}
						}
						if ($heading[$col] == "vendor_style"){
							if(!strpos($val, "-")){
								if(in_array($val, $check_vendor_style)){
									$thiserror = "$heading[$col] is a duplicate in row #$row";
								}
								else{
									$check_vendor_style[] = $val;
								}
							}
							else{
								$thiserror = "$heading[$col] has a - in row #$row";
							}
						}
						*/
					}

					// this column is a "size" column
					if ($col > $field_size_start && $col < $field_size_end) {
						$size_quantity += intval($val);
					}

					// this column is the total_quantity field
					if ($col == $field_size_end && $size_quantity != intval($val)) {
						$thiserror = "$heading[$col] value must match the sum of quantities for all sizes.";
					}
				}

				if($thiserror){
					$errors[] = $thiserror;
					$output .= Event::makecell($val, true);
				}
				else{
					$output .= Event::makecell($val);
				}
			}

			$output .= "<div style=\"clear:both;\"></div>";
		}

		//$errors[] = "you dont even know what youre doing!!!";
		if(count($errors)>0){
			$error_output="<h3>Spreadsheet Errors:</h3>" . implode($errors, '<br/>');
		}

		if($error_output){
			$output .= "<div>$error_output</div>";

			$output .= "<div style=\"clear:both;\"></div>";



			return "<div class=xls_holder_inner>$output</div>";
		}
		else{
			return "success";
		}

	}

	public static function getItems($eventId = null) {
		$items = null;
		if ($eventId) {
			$items = Item::find('all', array(
				'conditions' => array(
					'event' => array('$in' => array($eventId)
			))));
		}
		return $items;
	}
}


?>